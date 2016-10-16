<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends Catalog_controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('previewer_helper');
		$this->load->helper('general_functions_helper');
	}

	public function index($slug)
	{

		$this->load->model('project_model');
		$this->load->model('user_model');

		if (empty($slug))
		{
			$this->data['message'] = 'You must include either the Project Id or the Project Slug (i.e., "tom_sawyer_by_mark_twain" , without any other link info)';
			$this->_render('catalog/page');
			return;
		}

		//get project
		$this->data['project'] = $this->_get_project($slug);
		if (empty($this->data['project']))
		{
			$this->data['message'] = 'We weren\'t able to find that project. You must include either the Project Id or the Project Slug (i.e., "tom_sawyer_by_mark_twain" , without any other link info)';
			$this->_render('catalog/not_found');
			return;
		}


		// **** AUTHORS ****//
		$this->data['authors_string'] = '';

		$this->load->model('author_model');
		$this->data['authors'] = $this->author_model->get_author_list_by_project($this->data['project']->id, 'author');
		if (!empty($this->data['authors']))
		{
			$this->data['authors'] = array_slice($this->data['authors'], 0 , 20); //only show 20 authors on page

			$this->data['authors_string'] = $this->_authors_string($this->data['authors']);

		}	


		// **** TRANSLATORS ****//
		$this->data['translators'] = $this->author_model->get_author_list_by_project($this->data['project']->id, 'translator');
		if (!empty($this->data['translators']))
		{
			$this->data['translators'] = array_slice($this->data['translators'], 0 , 20); //only show 20 authors on page

			$this->data['authors_string'] .=  ', translated by ' . $this->_authors_string($this->data['translators']);			
		}	


		//var_dump($this->data['project']);

		// **** VOLUNTEERS ****//
		$this->data['volunteers'] = new stdClass();
		$this->data['volunteers']->bc = $this->user_model->get($this->data['project']->person_bc_id); 
		$this->data['volunteers']->mc = $this->user_model->get($this->data['project']->person_mc_id); 
		$this->data['volunteers']->pl = $this->user_model->get($this->data['project']->person_pl_id); 


		// **** MISC ****//
		$this->data['project']->project_type 	= (trim($this->data['project']->project_type) == 'solo')? 'solo' : 'group'; //keep on top - other values dependent on solo/group
		$this->data['project']->read_by			= $this->_read_by($this->data['project']);

		$this->data['project']->genre_list 		= $this->_genre_list($this->data['project']->id);

		$this->data['project']->group 			= $this->_project_group($this->data['project']->id);

		$this->data['project']->language 		= $this->_language($this->data['project']->language_id);


		// Build up the links

	    $this->load->model('project_url_model');
		$this->data['project']->project_urls = $this->project_url_model->order_by('order', 'asc')->get_many_by(array('project_id'=>$this->data['project']->id));


		$this->load->model('section_model');
		//$this->data['sections'] = $this->section_model->as_array()->get_many_by(array('project_id'=>$this->data['project']->id));
		$this->data['sections'] = $this->section_model->get_full_sections_info($this->data['project']->id);


		//var_dump($this->data['sections']);
		if (!empty($this->data['sections']))
		{
			foreach ($this->data['sections'] as $key => $section) {
				$this->data['sections'][$key]->reader = $this->_readers_string($section->readers);

				//if compilation, get author, language, source link
				if ($this->data['project']->is_compilation)
				{

					$this->data['sections'][$key]->author = null;
					$this->data['sections'][$key]->language = null;

					if ($section->author_id)
					{
						$this->data['sections'][$key]->author = $this->author_model->get($section->author_id);
					}	
					if ($section->language_id)
					{
						$this->load->model('language_model'); 
						$this->data['sections'][$key]->language = $this->language_model->get($section->language_id);
					}
					
				}	

			}
		}


		$this->data['search_category'] = 'page';
		$this->data['primary_key'] = 0;

		$this->_render('catalog/page');
		return;
	}
	



	function _project_group($project_id=0)
	{
		$this->load->model('group_model');
		$group = $this->group_model->get_group_by_project($project_id );

		return $group;
	}

	function _authors_string($authors)
	{

		foreach ($authors as $key => $author) {
			//$lastname = strtoupper(is_empty($author['last_name'], ''));
			$lastname = mb_convert_case(mb_strtolower($author['last_name']), MB_CASE_UPPER, "UTF-8");

			$string_array[] = '<a href="'.base_url(). 'author/' . $author['id']. '">' .is_empty($author['first_name'], '') . ' ' . $lastname. ' (' . is_empty($author['dob'] , '') .' - '. is_empty($author['dod'] , '') . ')' . '</a>' ;
		}

		if (count($string_array) < 4)
		{
			$string = implode(' and ', $string_array);
		}
		else
		{
			$string = 'Various';
		}	

		return $string;
	}

	function _read_by($project)
	{
		if (strtolower($project->project_type) == 'group') return 'LibriVox Volunteers';

		$readers = $this->project_model->get_project_readers($project->id);

		if ($readers) return '<a href="'. base_url().'reader/'.$readers[0]['reader_id'].'">'.$readers[0]['display_name'].'</a>';

		return 'Solo';
	}

	function _readers_string($readers)
	{

		if (empty($readers)) return '';

		if (count($readers) > 2) return 'Group';

		$html = '';
		foreach ($readers as $key => $reader) {
			$html .= '<a href="'. base_url().'reader/'.$reader->reader_id.'">'.$reader->display_name.'</a><br/>';
		}

		return $html;	

	}


	function _genre_list($project_id)
	{
		$genre_array = $this->project_model->get_genres_by_project($project_id, 'name', 'array');

		//limit to 3 genres
		$genre_array = array_slice($genre_array, 0, 3);

		if (empty($genre_array)) return '';

		foreach ($genre_array as $key => $genre) {
			//$genres[] = '<a href="#" class="js-genre_button" data-genre_id="'.$genre['id'].'">'.$genre['name']. '</a>';
			$genres[] = $genre['name'];
		}

		return implode(', ' , $genres);
	}

	function _author_list($project_id)
	{
		$author_list = $this->project_model->get_project_authors($project_id);

		if (empty($author_list)) return '';

		foreach ($author_list as $key => $author) {
			$authors[] = '<a href="'.base_url().'author/'.$author->id.'">'.$author->author. ' (' . is_empty($author->dob , '') . ' - ' . is_empty($author->dod , '')  .')</a>';  //('.  is_empty($author->dob , '') . ' - ' is_empty($author->dod , '')  .')
		}

		return implode(', ' , $authors);
	}

	function _language($language_id)
	{
		$this->load->model('language_model');
		return $this->language_model->get($language_id)->language;

	}


}		