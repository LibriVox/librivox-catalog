<?php defined('BASEPATH') OR exit('No direct script access allowed');

// We put all of our different file tests here for neatness & convenience

// We typically run these before uploading to Archive.org, but may run them earlier to give 
// visual clues in the table


class Librivox_filecheck{

	private $file_array;	

	private $validation_dir;

	public $test_array;

	private $project_type = 'solo';

	private $file_length = 300; //5minutes, in seconds

	private $project;

	private $errors;

	public function __construct($config=null)
	{
		$this->load->library('Librivox_id3tag');

		$this->file_array 	= array();
		$this->errors 		= array();

		$this->_set_tests(); //this way we can override which tests we want to run

		if(is_array($config)) $this->initialize($config);
	}

	public function initialize($config){
		if(!is_array($config)) return false;
		
		foreach($config as $key => $val){
			$this->$key = $val;
		}
	}	

	//lets us skip writing $this->ci-> everywhere
	public function __get($var)
	{
		return get_instance()->$var;
	}	


	public function load_project_map($validation_dir, $map, $project)
	{
		if (empty($validation_dir) || empty($map)) return false;

		$this->validation_dir = $validation_dir;

		$this->project 	= $project;

		$this->_set_file_array($map);

		$this->_set_project_type($this->project->project_type);

		$this->_run_tests();

		return array('file_array'=>$this->file_array,'errors'=>$this->errors);

	}

	function _set_tests()
	{

		$this->test_array = array( 			
			'bitrate', 
			'samplerate', 
			'permissions', 
			'mode' , 
			'tag_completeness', 
			'file_length',
			//'reader', 
			'id3v2' , 
			'chapter_name', 			
			'filename_chapter',  
			'tracknumber', 			
			'filename',			
			'album_tags'
		);

	}


	function _set_file_array($map)
	{
		sort($map);

		foreach ($map as $key=>$file_name)
		{

			$file_tags = $this->librivox_id3tag->_get_file_tags($this->validation_dir. $file_name);

			//var_dump($file_tags);

			// i think more useful than using the key...?
			$this->file_array[$key] = array(
				'file_name'	=> $file_name, 
				'album'		=> $file_tags['comments']['album'][0],
				'artist'	=> $file_tags['comments']['artist'][0],
				'chapter' 	=> $file_tags['comments']['title'][0],
				'track' 	=> $file_tags['comments']['track_number'][0],
				'length' 	=> $file_tags['playtime_seconds'],
				'bitrate'   => $file_tags['bitrate'],
				'sample'    => $file_tags['audio']['sample_rate'],
				'channelmode'  => $file_tags['audio']['channelmode'],
				'id3v2'		=> array_key_exists('id3v2', $file_tags),
			);

			unset($file_tags);
			//var_dump($this->file_array[$file_name]);
		}

	}

	function _set_project_type(&$project_type)
	{
		$this->project_type = (in_array($project_type, array(PROJECT_TYPE_POETRY_WEEKLY, PROJECT_TYPE_POETRY_FORTNIGHTLY)))?  'poem' : 'solo'; //solo is  a shortcut now for all other types
	}

	function _run_tests()
	{
		$this->first_album = ''; $i =0;//to compare if we have all the same album

		foreach ($this->file_array as $file_name => $file_array) {

			if (!$i) $this->first_album = $this->file_array[$file_name]['album'];
			$i++;
			
			foreach ($this->test_array as $test) {
				$this->file_array[$file_name]['tests'][$test] =  $this->{'check_'.$test}($file_array);

				//var_dump($this->file_array[$file_name]['tests'][$test]);
			}

		}

		//once we've finished
		$this->errors = $this->check_file_completeness($this->file_array, $this->project);

	}


	// File level tests

	function check_bitrate(&$file_array)
	{
		return ((int)$file_array['bitrate']  == 128000);
	}

	function check_samplerate(&$file_array)
	{
		return ((int)$file_array['sample']  == 44100);
	}

	//would indicate a bug
	function check_permissions(&$file_array)
	{
		return is_writeable($this->validation_dir. $file_array['file_name']);
	}		


	function check_mode(&$file_array)
	{
		return ($file_array['channelmode']  == 'mono');
	}

	// check album, artist & chapter tags are present
	function check_tag_completeness(&$file_array)
	{
		//flip the boolean - TRUE means it passes
		return !(empty($file_array['album'])  || empty($file_array['artist']) || empty($file_array['chapter']));
	}

	function check_file_length(&$file_array)
	{
		return ((int)$file_array['length']  >= (int)$this->file_length);
	}

	function check_reader($project_id)
	{
		//check all sections in project to be sure there is a reader
		$this->load->model('section_model');
		return $this->section_model->check_section_readers($project_id);
	}

	function check_id3v2(&$file_array)
	{
		return $file_array['id3v2'];
	}	

	function check_chapter_name(&$file_array)
	{
		$pattern = ($this->project_type == 'poem') ? '/^(.*)_.*?$/' : '/^[0-9]+.*$/';

		preg_match($pattern, $file_array['chapter'], $matches);  //for poems - 
		return (!empty($matches));
	}

	function check_filename(&$file_array)
	{
		$pattern = ($this->project_type == 'poem') ? '/^(.+)_.*?\.mp3$/' : '/^(.+)_[\d-]+(.*)\.mp3$/';

		preg_match($pattern, $file_array['file_name'], $matches);  //for poems - 
		return (!empty($matches));		
	}

	function check_filename_chapter(&$file_array)
	{
		// we may need to revist this. looks at "letters_of_two_brides_02_balzac_128kb.mp3" & "02 - Chapter 2" and tries to compare "02"
		$file_name_array = explode('_' , $file_array['file_name']);
		$chapter_array = explode('-' , $file_array['chapter']); 

		return ($file_name_array[count($file_name_array) -3]  == trim($chapter_array[0]));
	}	

	// checks filename contains same number as track number
	function check_tracknumber(&$file_array)
	{
		$file_name_array = explode('_' , $file_array['file_name']);
		$compare = (int)$file_name_array[count($file_name_array) -3] + $this->project->has_preface;
		return ($compare == (int)$file_array['track']);		
	}	

	//check all files have same album
	function check_album_tags(&$file_array)
	{
		return ($this->first_album == $file_array['album']);
	}


	function check_file_completeness($file_array, $project)
	{
		$errors = array();

		$first = abs($project->has_preface-1);

		//var_dump($file_array);

		for($i = 0; $i < $project->num_sections; $i++)
		{

			if (!isset($file_array[$i])) 
			{
				$errors[] = 'Expected ' . $project->num_sections .' files: file #'. ($i + $project->has_preface). ' does not appear to exist. Please check all sections are loaded.';
				continue;
			}	
			$curr_file = $file_array[$i];

			$file_name_array = explode('_' , $curr_file['file_name']);
			$section_number = (int)$file_name_array[count($file_name_array) -3];

			//echo $first.':'.$section_number.'::';
			//does the counter match the "counter" in the file name?
			if ($first != $section_number)
			{
				//there may be a problem
				$file_name_array[count($file_name_array) -2] = str_pad($first, 2, '0', STR_PAD_LEFT);
				$file_should_be = implode('_', $file_name_array);

				$errors[] = 'Expecting file #'. $first. ' to have name ' . $file_should_be. ' DEBUG:('.$first .'::'.$section_number.'::'.$curr_file['file_name'].')';
			}

			//does the counter match the Chapter prefix?
			$chapter_array = explode('-' , $curr_file['chapter']); 
			$chapter_number = trim($chapter_array[0]);
			if ($first != $chapter_number)
			{
				$errors[] = 'Expecting file #'. $first. ' to have chapter number ' . str_pad($first, 2, '0', STR_PAD_LEFT). ' DEBUG:('.$first .'::'.$chapter_number.')';
			}	

			//does the counter (adjusted) match the track number?
			if (($first + $project->has_preface) != $curr_file['track'])
			{
				$errors[] = 'Expecting file #'. $first. ' to have track number ' . $first. ' DEBUG:('.$first + $project->has_preface .'::'.$curr_file['track'].')';
			}	

			$first++;
		}

		return $errors;

	}



	//i think we don't want this anymore? artists can be different in compilations

	/*
	function check_artist_tags()
	{
		
	}

	function check_chapter_tags()
	{
		
	}
	*/

	




}