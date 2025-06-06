<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reader extends Catalog_controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('general_functions_helper');
	}

	public function index($reader_id)
	{
		if (empty($reader_id)) {
			show_404();
		}

		$this->load->model('user_model');
		$this->data['reader'] = $this->user_model->as_array()->get($reader_id);

		//var_dump($this->data['reader']);
		//echo $this->db->last_query();

		$this->data['search_category'] = 'reader';

		$this->data['primary_key'] = $reader_id;

		$this->load->model('section_readers_model');
		$sections = $this->section_readers_model->get_many_by('reader_id', $reader_id);
		$this->data['sections'] = count($sections);

		$matches = $this->_get_all_reader($reader_id);
		$this->data['matches'] = count($matches);

		$this->data['page_title'] = 'Works read by '. $this->data['reader']['display_name'];

		$this->data['search_order'] = $this->input->get('search_order');

		$this->_render('catalog/reader');
		return;
	}

	function get_results()
	{
		//collect - search_category, sub_category, page_number, sort_order
		$input = $this->input->get(null, true);
		$reader_id = $input['primary_key'];
		$search_order = $input['search_order'];

		if (empty($reader_id)) {
			show_error('A primary_key (reader ID) must be supplied', 400);
		}

		//format offset
		$offset = ($input['search_page'] - 1) * CATALOG_RESULT_COUNT;

		// go get results
		$results = $this->_get_all_reader($reader_id, $offset, CATALOG_RESULT_COUNT, $search_order, $input['project_type']);

		$full_set = $this->_get_all_reader($reader_id, 0, 1000000, 'alpha', $input['project_type']);
		//$retval['sql'] = $this->db->last_query();

		// go format results
		$retval['results'] = $this->_format_results($results, 'title');

		//pagination
		$page_count = (count($full_set) > CATALOG_RESULT_COUNT) ? ceil(count($full_set) / CATALOG_RESULT_COUNT) : 0;
		$retval['pagination'] = (empty($page_count))
							  ? ''
							  : $this->_format_pagination(
								  $input['search_page'],
								  $page_count,
								  'get_results',
								  function ($page) use ($reader_id, $search_order) {
									  $query_string = http_build_query(array(
										  'search_page' => $page,
										  'search_order' => $search_order,
									  ));
									  return '/reader/' . $reader_id . '/?' . $query_string;
								  },
							  );

		$retval['status'] = 'SUCCESS';

		//$retval['page_count'] = $page_count;
		//$retval['full_set'] = $full_set;

		//return - results, pagination
		if ($this->input->is_ajax_request())
		{
			header('Content-Type: application/json;charset=utf-8');
			echo json_encode($retval);
			return;
		}
	}

	function _get_all_reader($reader_id, $offset = 0, $limit = 1000000, $search_order = 'alpha', $project_type = 'either')
	{
		$params['reader_id'] = $reader_id;
		$params['offset'] = $offset;
		$params['limit'] = $limit;
		$params['search_order'] = $search_order;
		$params['project_type'] = $project_type;

		$this->load->model('project_model', 'model');
		$projects = $this->model->get_projects_by_reader($params);

		$this->load->model('author_model');

		// we'll be changing this query, but for now: the full set query for pagination doesn't need the authors
		if ($params['limit'] == 1000000)
		{
			return $projects;
		}

		foreach ($projects as $key => $project)
		{
			$projects[$key]['author_list'] = ' ';
			$authors = $this->author_model->get_author_list_by_project($project['id'], 'author');
			if (!empty($authors))
			{
				$authors = array_slice($authors, 0, 20); //only show 20 authors on page
				$projects[$key]['author_list'] = $this->_authors_string($authors);
			}
		}

		return $projects;
	}
}
