<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Author extends Catalog_controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('general_functions_helper');
	}

	public function index($author_id)
	{
		if (empty($author_id)) {
			show_404();
		}

		$this->load->model('author_model');
		$this->data['author'] = $this->author_model->get($author_id);
		$this->data['edit_link'] = $this->_get_edit_link($author_id);

		$this->data['search_category'] = 'author';

		$this->data['primary_key'] = $author_id;

		$matches = $this->_get_all_author($author_id);
		$this->data['matches'] = count($matches);

		$this->data['page_title'] = 'Works by '. format_author($this->data['author']);

		$this->data['search_order'] = $this->input->get('search_order');

		$this->_render('catalog/author');
		return;
	}

	function get_results()
	{
		//collect - search_category, sub_category, page_number, sort_order
		$input = $this->input->get(null, true);
		$author_id = $input['primary_key'];
		$search_order = $input['search_order'];

		if (empty($author_id)) {
			show_error('A primary_key (author ID) must be supplied', 400);
		}

		//format offset
		$offset = ($input['search_page'] - 1) * CATALOG_RESULT_COUNT;

		// go get results
		$results = $this->_get_all_author($author_id, $offset, CATALOG_RESULT_COUNT, $search_order, $input['project_type']);

		$full_set = $this->_get_all_author($author_id, 0, 1000000, 'alpha', $input['project_type']);
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
								  function ($page) use ($author_id, $search_order) {
									  $query_string = http_build_query(array(
										  'search_page' => $page,
										  'search_order' => $search_order,
									  ));
									  return '/author/' . $author_id . '/?' . $query_string;
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

	function _get_all_author($author_id, $offset = 0, $limit = 1000000, $search_order = 'alpha', $project_type = 'either')
	{
		$params['author_id'] = $author_id;
		$params['offset'] = $offset;
		$params['limit'] = $limit;
		$params['search_order'] = $search_order;
		$params['project_type'] = $project_type;

		$this->load->model('project_model', 'model');
		$projects = $this->model->get_projects_by_author($params);

		//echo $this->db->last_query();

		$this->load->model('author_model');
		$this->load->model('section_model');

		foreach ($projects as $key => $project)
		{
			if ($project['primary_type'] == 'section')
			{
				$section = $this->section_model->get($project['primary_key']);

				if (empty($section) || !isset($section->author_id))
				{
					$projects[$key]['author_list'] = 'n/a';
					continue;
				}

				//echo $section->author_id. '::';

				$authors = $this->author_model->get_author_list($section->author_id);
				if (empty($authors))
				{
					$projects[$key]['author_list'] = 'n/a';
				}
				else
				{
					$authors = array_slice($authors, 0, 20); //only show 20 authors on page
					$projects[$key]['author_list'] = $this->_authors_string($authors);
				}
			}
			else
			{
				$authors = $this->author_model->get_author_list_by_project($project['primary_key'], 'author');
				if (empty($authors))
				{
					$projects[$key]['author_list'] = 'n/a';
				}
				else
				{
					$authors = array_slice($authors, 0, 20); //only show 20 authors on page
					$projects[$key]['author_list'] = $this->_authors_string($authors);
				}
			}
		}

		return $projects;
	}

	function _get_edit_link($author_id)
	{
		$link = '';
		$auth_checker = new Librivox_auth();

		if (empty($author_id))
		{
			return $link;
		}

		$user_id = $auth_checker->get_user_id();
		if ($user_id < 1) {
			return $link;
		}

		//check permissions
		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);		
		if ($auth_checker->has_permission($allowed_groups, $user_id))
		{
			$link = base_url() . 'admin/author_manager/' . $author_id ;
			return $link;
		}

		return $link;
	}
}
