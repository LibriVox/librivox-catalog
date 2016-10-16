<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Catalog_controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('previewer_helper');
		$this->load->helper('general_functions_helper');
	}

	public function index($group_id)
	{
		
		$this->load->model('group_model');
		$this->data['group'] = $this->group_model->get($group_id); 
		

		$this->data['search_category'] = 'group';

		$this->data['primary_key'] = $group_id;

		$matches = $this->_get_all_group_projects($group_id, 0, 1000000);
		$this->data['matches'] = count($matches);

		$this->_render('catalog/group');
		return;

	}

	function get_results()
	{
		//collect - search_category, sub_category, page_number, sort_order
		$input = $this->input->get_post(null, true);

		//format offset
		$offset = ($input['search_page'] -1 ) * CATALOG_RESULT_COUNT;

		// go get results
		$results = $this->_get_all_group_projects($input['primary_key'], $offset, CATALOG_RESULT_COUNT);

		$full_set = $this->_get_all_group_projects($input['primary_key'], 0, 1000000);
		//$retval['sql'] = $this->db->last_query();

		// go format results
		$retval['results'] = $this->_format_results($results, 'title');

		//pagination
		$page_count = (count($full_set) > CATALOG_RESULT_COUNT) ? ceil(count($full_set)/ CATALOG_RESULT_COUNT): 0;
		$retval['pagination'] = (empty($page_count)) ? '' : $this->_format_pagination($input['search_page'], $page_count);   // $first_page, $page_count

		$retval['status']  = 'SUCCESS';

		//return - results, pagination
		if ($this->input->is_ajax_request())
		{
			header('Content-Type: application/json;charset=utf-8');
			echo json_encode($retval); return;
		}		
	}

	function _get_all_group_projects($group_id, $offset=0, $limit=1000000)
	{
		$params['group_id'] 		= $group_id;
		$params['offset'] 			= $offset;
		$params['limit'] 			= $limit;

		$this->load->model('project_model');
		$projects =  $this->project_model->get_projects_by_group($params);		

		foreach ($projects as $key => $project) {			
			 $projects[$key]['author_list'] = $this->_author_list($project['id']);
		}

		return $projects;

	}
	
}		