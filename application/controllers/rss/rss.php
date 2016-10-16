<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rss extends Catalog_controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index($slug)
	{
		

		// eventually add a caching solution here


		//get project data
		if (empty($slug))
		{
			$this->data['message'] = 'You must include either the Project Id or the Project Slug (i.e., "tom_sawyer_by_mark_twain" , without any other link info)';
			$this->_render('catalog/page');
			return;
		}

		//get project
		$this->load->model('project_model');

		$this->data['project'] = $this->_get_project($slug);
		if (empty($this->data['project']))
		{
			$this->data['message'] = 'We weren\'t able to find that project. You must include either the Project Id or the Project Slug (i.e., "tom_sawyer_by_mark_twain" , without any other link info)';
			$this->_render('catalog/not_found');
			return;
		}


		// build feed

		$this->_build_feed();


	}


	function _build_feed()
	{

		$this->_create_title_bar();

		$this->_build_sections();


		$this->load->view('rss/rss', $this->data, FALSE);

	}

	function _create_title_bar()
	{
		$this->data['project']->title_bar = $this->data['project']->title;

		if (!empty($this->data['project']->title_prefix))
		{
			$this->data['project']->title_bar .= ', '. $this->data['project']->title_prefix;
		}

		$this->data['authors_string'] = '';

		$this->load->model('author_model');
		$this->data['authors'] = $this->author_model->get_author_list_by_project($this->data['project']->id, 'author');
		if (!empty($this->data['authors']))
		{
			$this->data['authors'] = array_slice($this->data['authors'], 0 , 20); //only show 20 authors on page

			$this->data['authors_string'] = $this->_authors_string_no_link($this->data['authors']);
		}	

		if (!empty($this->data['authors_string']))
		{
			$this->data['project']->title_bar .= ' by '. $this->data['authors_string'];
		}				
		
	}

	function _build_sections()
	{

		$this->load->model('section_model');
		$this->data['sections'] = $this->section_model->get_full_sections_info($this->data['project']->id);

	}

	// *** Latest Releases Feed *** //


	function latest_releases()
	{

		$this->load->model('project_model');
		$this->data['projects'] = $this->project_model->get_lastest_releases(10);

        if($this->data['projects'])
        {
			foreach ($this->data['projects'] as $key => $project) {

				$this->data['projects'][$key]->title_bar = $this->data['projects'][$key]->title;

				if (!empty($this->data['projects'][$key]->title_prefix))
				{
					$this->data['projects'][$key]->title_bar .= ', '. $this->data['projects'][$key]->title_prefix;
				}

				$this->data['projects'][$key]->authors_string = '';

				$this->load->model('author_model');
				$this->data['projects'][$key]->authors = $this->author_model->get_author_list_by_project($this->data['projects'][$key]->id, 'author');

				if (!empty($this->data['projects'][$key]->authors))
				{
					$this->data['projects'][$key]->authors = array_slice($this->data['projects'][$key]->authors, 0 , 20); //only show 20 authors on page

					$this->data['projects'][$key]->authors_string = $this->_authors_string_no_link($this->data['projects'][$key]->authors);
				}	

				if (!empty($this->data['projects'][$key]->authors_string))
				{
					$this->data['projects'][$key]->title_bar .= ' by '. $this->data['projects'][$key]->authors_string;
				}
				//$this->data['projects'][$key]->authors = $this->project_model->get_project_authors($project->id);
			}
        }

        //var_dump($this->data['projects']);

        $this->load->view('rss/latest_releases', $this->data, FALSE);

	}


}