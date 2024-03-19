<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Keywords extends Catalog_controller
/**
When passed a keywords_id, display a list of projects that have that keyword
*/

{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('general_functions_helper');
	}

	public function index($keywords_id)
	{
		if (empty($keywords_id)) {
			show_404();
		}
		$this->load->model('keyword_model');
		$this->data['keywords'] = $this->keyword_model->get($keywords_id);
		$this->data['search_category'] = 'keywords';
		$this->data['primary_key'] = $keywords_id;
		$params['keywords_id'] = $keywords_id;		
		$params['offset'] = 0;
		$params['limit'] = 1000000;
		$matches = $this->_get_projects_by_keywords_id($params);
		$this->data['matches'] = count($matches);		
		$this->_render('catalog/keywords');
		return;
	}
	
	function get_results()
	{

		//collect - search_category, sub_category, page_number, sort_order
		$input = $this->input->get(null, true);
		
		error_log("PJD " . "near start of get_results() for Keywords Controller");
		$backtrace = (new Exception)->getTraceAsString();
		error_log($backtrace);
		$encoded = json_encode($input);
		error_log("PJD The following is content of input: " . $encoded);
		
		$params['keywords_id'] = $input['primary_key'];

		if (empty($params['keywords_id'])) {
			show_error('A primary_key (keywords ID) must be supplied', 400);
		}

		//format offset
		$params['offset'] = ($input['search_page'] - 1) * CATALOG_RESULT_COUNT;
		
		// format limit
		$params['limit'] = CATALOG_RESULT_COUNT;

		// go get results
		$results = $this->_get_projects_by_keywords_id($params);
		
		// get full set by calling same function with different parameters
		$params['offset'] = 0;
		$params['limit'] = 1000000;

		$full_set = $this->_get_projects_by_keywords_id($params);

		// go format results
		$retval['results'] = $this->_format_results($results, 'title');

		//pagination
		$page_count = (count($full_set) > CATALOG_RESULT_COUNT) ? ceil(count($full_set) / CATALOG_RESULT_COUNT) : 0;
		$retval['pagination'] = (empty($page_count)) ? '' : $this->_format_pagination($input['search_page'], $page_count);

		$retval['status'] = 'SUCCESS';

		//return - results, pagination
		if ($this->input->is_ajax_request())
		{	
			header('Content-Type: application/json;charset=utf-8');
			echo json_encode($retval);
			return;
		}
	}
	
	function _get_projects_by_keywords_id($params)
	{
		$this->load->model('project_model');
		$projects = $this->project_model->get_projects_by_keywords_id($params);
		
		foreach ($projects as $key => $project)
		{
			$projects[$key]['author_list'] = $this->_author_list($project['id']);
		}

		return $projects;
	}
}
