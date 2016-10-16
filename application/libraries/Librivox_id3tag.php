<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Librivox_id3tag{

	public $sections_array = array();

	//lets us skip writing $this->ci-> everywhere
	public function __get($var)
	{
		return get_instance()->$var;
	}


    function _get_file_tags($file_name)
    {
    	$this->load->library('getid3_wrapper');

    	$page_encoding = 'UTF-8';
    	$getID3 = new getID3;
		$getID3->setOption(array('encoding' => $page_encoding));

		$file_data = $getID3->analyze($file_name);

		// tags are per file encoding, so this copies from whatever the subarray is to a consistent location
		getid3_lib::CopyTagsToComments($file_data); 

        if (empty($file_data['comments']['title']))
        {
            $file_data['comments']['title'][0] = '';
        } 
        if (empty($file_data['comments']['artist']))
        {
            $file_data['comments']['artist'][0] = '';
        }
		return $file_data;
    }


    function _write_tag($file, $tag_data)
    {
    	$this->load->library('getid3_wrapper');
		$this->getid3_wrapper->write_tag($file, $tag_data);
    }

	
    function _update_name($local_file, $post, $has_preface, $track_number)
    {

        $pad_count = ($post['num_sections'] > 99) ? 3 : 2;

    	$array[] 	= is_empty(trim($post['name_part_1']));    	
    	$array[] 	= str_pad($track_number - $has_preface, $pad_count, '0', STR_PAD_LEFT); //pad    	
    	$array[] 	= is_empty(trim($post['name_part_2']));

    	array_filter($array);
    	$new_name = implode('_', $array);

    	$file_array = explode('/',$local_file);

    	$file_name = end($file_array);
    	array_pop($file_array);
    	array_push($file_array, $new_name);
    	$new_local_file = implode('/', $file_array). '.'.pathinfo($local_file, PATHINFO_EXTENSION);

    	$copied = rename($local_file, $new_local_file); 

        $this->_update_section_by_filename($file_name, array('file_name'=>$new_name. '.'.pathinfo($local_file, PATHINFO_EXTENSION)));
    }

    function _update_section($section_id, $update)
    {
        $this->load->model('section_model');
        $this->section_model->update($section_id, $update);

    }

    function _update_section_by_filename($file_name, $update)
    {
        $this->load->model('section_model');
        $this->section_model->update_by(array('file_name'=>$file_name), $update);
    }

    function _update_tags($file_array, $post)
    {
		$tag_data = array(
		    'title'   => array($file_array['comments']['title'][0]),  //title is for chapter
		    'artist'  => array($file_array['comments']['artist'][0]),
		    'album'   => array($file_array['comments']['album'][0]),
		    'genre'   => array('speech'),
		    'track'   => array($file_array['comments']['track_number'][0]),
		);

        //swap out the set values for the new post values
		$keys = array_keys($post);
		foreach ($keys as $key)
		{
			$tag_data[$key] = array($post[$key]);
		}	
		return $tag_data;
    }

    function _update_tracks($file_array, $track)
    {

        $tag_data = array(
            'title'   => array($file_array['comments']['title'][0]),  //title is for chapter
            'artist'  => array($file_array['comments']['artist'][0]),
            'album'   => array($file_array['comments']['album'][0]),
            'genre'   => array('speech'),
            'track'   => array($track),
        );
        return $tag_data;
    }

    function _build_tags($project, $section)
    {
    	$track = $section->section_number + $project->has_preface; //track 0 --> 1; track 1 --> 1

    	$title = $this->_build_chapter_title($project, $section);

		// populate data array
		return $tag_data = array(
		    'title'   => array($title),  //title is for chapter
		    'artist'  => array($section->author),
		    'album'   => array($project->full_title),
		    //'year'    => array('2004'),
		    'genre'   => array('speech'),
		    'playtime_string' => array('0:00'),
		    'track'   => array($track),
		    //language??
		);

    }

    function _create_files_table($dir, $map = array(), $freeze = false)
    {

    	$this->load->library('getid3_wrapper');
        $this->load->library('Librivox_mp3gain');

        $mp3gain_default = 89; 

    	$page_encoding = 'UTF-8';
    	$getID3 = new getID3;
		$getID3->setOption(array('encoding' => $page_encoding));

		$files_array = array();

		if (!empty($map))
		{
			foreach($map as $file)
			{
				if (empty($file)) break;

				$file_name = $dir. $file;
				$file_data = $getID3->analyze($file_name);

				// tags are per file encoding, so this copies from whatever the subarray is to a consistent location
				getid3_lib::CopyTagsToComments($file_data); 


                //analyze volume
                $mp3gain_result = $this->librivox_mp3gain->analyze_file($dir, $file);

				$file_array = $this-> _create_file_array($freeze); //prototype
				
				//var_dump($file_data);
				$file_array['edit']			= '<i data-file_name="'.$file_data['filename'].'" class="icon-search meta_data" style="cursor:pointer"></i>';
				$file_array['filename'] 	= $file_data['filename'];	
				$file_array['filesize'] 	= number_format($file_data['filesize']);	
				$file_array['playtime'] 	= $file_data['playtime_string'];	
				$file_array['volume'] 		= $mp3gain_default - number_format($mp3gain_result[2], 1);
				$file_array['bitrate'] 		= ceil($file_data['audio']['bitrate']/ 1000);
				$file_array['sample'] 		= number_format($file_data['audio']['sample_rate']);

				$file_array['track'] 		= '';
                $file_array['delete']       = '<i data-file_name="'.$file_data['filename'].'" class="icon-remove delete_file" style="cursor:pointer"></i>';


				if (!empty($file_data['comments']))
				{
					$file_array['album'] 		= empty($file_data['comments']['album'][0])? null: $file_data['comments']['album'][0];
					$file_array['artist'] 		= empty($file_data['comments']['artist'][0])? null: $file_data['comments']['artist'][0];
					$file_array['chapter'] 		= empty($file_data['comments']['title'][0])? null: $file_data['comments']['title'][0];
					$file_array['track'] 		= empty($file_data['comments']['track_number'][0])? '': $file_data['comments']['track_number'][0];				
				}

				// this is a bit of a crutch - we don't actually stamp the file itself with a section id, so we use this to maintain
				// a link from file to section
                $section_id = array_search($file_array['filename'], $this->sections_array); 
                if ($section_id !== FALSE)
                {
                    $file_array['section']         = $section_id;
                }
                else
                {
                    $file_array['section']         = '<i data-file_name="'.$file_data['filename'].'" title="Link to section" class="icon-share link_section" style="cursor:pointer"></i>';                
                }


                if ($freeze)
                {
                    $file_array['delete']   = '';
                    $file_array['edit']     = '';
                    $file_array['section']  = '';
                }                   


				$chapter[]  = $file_array['chapter'];

				array_push($files_array, $file_array);

			}	

			// Sort the data by chapter number
			array_multisort($chapter, SORT_ASC,  $files_array); 
			
		}	
		$this->load->library('table');

		$this->table->set_heading(array_keys($this->_create_file_array($freeze)));
		$this->table->set_template(array ('table_open'=> '<table class="table table-striped table-hover table-condensed">' )); 
		return  $this->table->generate($files_array);


    }



    function _create_file_array($freeze)
    {
        if (!$freeze)
        {
            $file_array['section']      = null;
            $file_array['edit']         = null;            
        }    

		$file_array['filename'] 	= null;
		$file_array['filesize'] 	= null;
		$file_array['playtime'] 	= null;
		$file_array['volume'] 		= null; 
		$file_array['bitrate'] 		= null;
		$file_array['sample'] 		= null; 
		$file_array['album'] 		= null;
		$file_array['artist'] 		= null;
		$file_array['chapter'] 		= null;
		$file_array['track'] 		= null;
        
        if (!$freeze)
        {
            $file_array['delete']       = null;           
        }  

		return $file_array;
    }




    function _build_chapter_title($project, $section)
    {
        $pad_count = ($project->num_sections > 99) ? 3 : 2;

    	$title_prefix = trim(substr($section->title, 0, $pad_count));
    	if ($title_prefix == $section->section_number) return $section->title;

    	return str_pad($section->section_number, $pad_count, '0', STR_PAD_LEFT) . ' - ' .$section->title;

    }

}