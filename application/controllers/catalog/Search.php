<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends Catalog_controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('general_functions_helper');
		$this->load->helper('inflector');

		set_time_limit(0);
		ini_set('memory_limit', '-1');
	}

	public function _remap($method, $params = array())
	{
		//clear out old query string crud
		$this->session->unset_userdata('query');

		if (isset($_GET['search_form']) && ($_GET['search_form'] == 'advanced'))
		{
			unset($_GET['search_form']);
			$this->session->set_userdata('query', $_GET);
		}

		if (isset($_GET['search_form']) && ($_GET['search_form'] == 'get_results'))
		{
			//unset($_GET['search_form']);
			$this->session->set_userdata('query', $_GET);
		}

		if (method_exists($this, $method))
		{
			//our logic - if
			if (in_array($method, array('index')) && !empty($params))
			{
				$this->session->set_userdata('search_category', $params[0]);
				if ($params[0] == 'q') $this->session->set_userdata('search_value', $params[1]);
				redirect(base_url('search')); //yes, redirect - the whole point of this is to force the url
			}

			return call_user_func_array(array($this, $method), $params);
		}
		show_404();
	}

	public function index()
	{
		$search_category = $this->session->userdata('search_category');
		$this->session->unset_userdata('search_category');

		$this->data['advanced_search'] = 0;
		if ($search_category == 'advanced_search')
		{
			$this->data['advanced_search'] = 1;
		}

		$this->data['search_value'] = '';
		if ($search_category == 'q')
		{
			$this->data['search_value'] = $this->session->userdata('search_value');
			$this->session->unset_userdata('search_value');
		}

		//for advanced search from url
		$query = $this->session->userdata('query');

		//var_dump($query);var_dump($search_category);return;

		$proto = array(
			'title' => '',
			'author' => '',
			'reader' => '',
			'keywords' => '',
			'genre_id' => 0,
			'status' => 'all',
			'project_type' => 'either',
			'recorded_language' => '',
			'sort_order' => 'catalog_date',
			'search_page' => 1,
		);

		if (!empty($query))
		{
			$this->data['advanced_search'] = 2;

			if (isset($query['q']))
			{
				$this->data['search_value'] = $this->security->xss_clean($query['q']);
			}

			//this is for the "browsing searches"
			if (isset($query['search_category']) && isset($query['search_form']) && $query['search_form'] == 'get_results')
			{
				if ($query['search_category'] == 'genre') $query['genre_id'] = $query['primary_key'];

				if ($query['search_category'] == 'language') $query['recorded_language'] = $query['primary_key'];

				$search_category = $query['search_category'];

				$this->data['advanced_search'] = 3;
			}
		}

		$query = (empty($query)) ? array() : $query;
		$this->data['advanced_search_form'] = array_merge($proto, $query);

		//var_dump($query);
		//var_dump($this->data['advanced_search_form']);return;

		$this->data['search_category'] = (empty($search_category)) ? 'author' : $search_category;
		$this->data['primary_key'] = (empty($query['primary_key'])) ? 0 : $query['primary_key'];
		$this->data['search_page'] = $this->data['advanced_search_form']['search_page'];

		$this->data['statuses'] = array('all' => 'All', 'complete' => 'Complete', 'in_progress' => 'In Progress', 'open' => 'Open');
		$this->data['project_type'] = array('either' => 'Either', 'solo' => 'Solo', 'group' => 'Group');
		$this->data['sort_order'] = array('catalog_date' => 'Date Released', 'alpha' => 'Alphabetical');

		$this->load->helper('form_helper');

		$this->load->config('librivox');
		$this->data['languages'] = $this->config->item('languages');

		$this->load->model('language_model');
		$this->data['recorded_languages'] = full_languages_dropdown('recorded_language', $this->data['advanced_search_form']['recorded_language'], true);

		$this->load->model('genre_model', 'model');
		$genres = $this->model->order_by('lineage', 'asc')->as_array()->get_all();

		$this->data['genres'][0] = '';
		foreach ($genres as $key => $genre)
		{
			$this->data['genres'][$genre['id']] = $this->model->get_full_name_string($genre['id'], $separator = ' > ');
		}

		$this->session->unset_userdata('advanced_search_form');

		$this->_render('catalog/search');
		return;
	}

	public function advanced_search()
	{
		$input = $this->input->get(null, true);

		if (!empty($input['q']))
		{
			//this is the normal search - set up for author, reader, title search
			$input['author'] = $input['reader'] = $input['title'] = $input['q'];
		}

		$input['search_page'] = (empty($input['search_page'])) ? 1 : $input['search_page'];

		$this->load->library('Librivox_search');
		$this->load->library('Librivox_simple_search');

		$input['offset'] = ($input['search_page'] - 1) * CATALOG_RESULT_COUNT;
		$input['limit'] = CATALOG_RESULT_COUNT;

		if (!empty($input['q']))
		{
			//$this->data['results'] = $this->librivox_search->advanced_search($input);
			$this->data['results'] = $this->librivox_simple_search->simple_search($input);
		}
		else
		{
			$this->data['results'] = $this->librivox_search->advanced_title_search($input);
		}

		//$retval['sql'] = $this->db->last_query();

		$retval['status'] = 'SUCCESS';

		$retval['results'] = 'No results found';
		$page_count = 0;

		if (count($this->data['results']) > 0)
		{
			$retval['results'] = $this->_manage_result_set($this->data['results']);

			//pagination
			$full_count = $this->data['results'][0]['full_count'];

			$page_count = ($full_count > CATALOG_RESULT_COUNT) ? ceil($full_count / CATALOG_RESULT_COUNT) : 0;
		}

		$retval['pagination'] = (empty($page_count)) ? '' : $this->_format_pagination($input['search_page'], $page_count, 'get_advanced_results');

		$retval['search_page'] = $input['search_page'];

		if ($this->input->is_ajax_request())
		{
			header('Content-Type: application/json;charset=utf-8');
			echo json_encode($retval);
			return;
		}
	}

	function _manage_result_set($results)
	{
		// Here's where we get a little "different" - we want to run this as a single, non-UNION query, but we need to distinguish
		// which blade to use for each record. Since we are paginating, we'll only have CATALOG_RESULT_COUNT so we'll iterate over
		// the results & check WHY each row fit the criteria, then use that blade. Order of precedence:

		$retval['results'] = '';
		foreach ($results as $item)
		{
			// this will help to speed things up considerably - only titles uses author info, so here we will
			// iterate over the 25 projects we need to look up & also format them in case there are multiple
			// saves a lot of headaches throughout

			if ($item['blade'] == 'title')
			{
				$this->load->model('author_model');
				$authors = $this->author_model->get_author_list_by_project($item['id'], 'author');
				if (!empty($authors))
				{
					$authors = array_slice($authors, 0, 20); //only show 20 authors on page
					$item['author_list'] = $this->_authors_string($authors);
				}
			}

			$retval['results'] .= $this->_format_blade($item, $item['blade']);
		}

		return $retval['results'];
		//$retval['results'] = $this->_format_results($this->data['results'], 'title');

	}

	public function get_results()
	{
		//collect - search_category, sub_category, page_number, sort_order
		$input = $this->input->get(null, true);

		$retval['input'] = $input;

		//var_dump($input);

		//a little spaghetti if genre or language AND primary key, use titles & search that category. Got it?
		$sub_category = false;
		if (in_array($input['search_category'], array('genre', 'language')) && $input['primary_key'])
		{
			$sub_category = $input['search_category'];
			$input['search_category'] = 'title';
			//$retval['subcat'] = $sub_category;
		}

		//var_dump($input);

		//format offset
		$offset = ($input['search_page'] - 1) * CATALOG_RESULT_COUNT;

		$params['offset'] = $offset;
		$params['limit'] = CATALOG_RESULT_COUNT;
		$params['sub_category'] = $sub_category;
		$params['primary_key'] = $input['primary_key'];
		$params['search_order'] = isset($input['search_order']) ? $input['search_order'] : 'alpha';
		$params['project_type'] = isset($input['project_type']) ? $input['project_type'] : 'either';

		//var_dump($params);

		// go get results
		$function = "_search_{$input['search_category']}";
		$results = $this->$function($params);
		//$retval['sql'] = $this->db->last_query();

		//return;

		// go format results
		$retval['results'] = $this->_format_results($results, $input['search_category']);

		//temp (? - is anything ever temp?) fix to allow all items only for genres, until we make a accordian system
		if (in_array($input['search_category'], array('genre', 'language')))
		{
			$retval['pagination'] = '';
		}
		else
		{
			$params['offset'] = 0;
			$params['limit'] = 1000000;

			$full_set = $this->$function($params);

			//pagination
			$page_count = (count($full_set) > CATALOG_RESULT_COUNT) ? ceil(count($full_set) / CATALOG_RESULT_COUNT) : 0;
			$retval['pagination'] = (empty($page_count)) ? '' : $this->_format_pagination($input['search_page'], $page_count, 'get_results');
		}

		$retval['status'] = 'SUCCESS';

		//return - results, pagination
		if ($this->input->is_ajax_request())
		{
			header('Content-Type: application/json;charset=utf-8');
			echo json_encode($retval);
			return;
		}

		return $retval;
	}

	function _search_author($params)
	{
		$this->load->model('author_model', 'model');
		return $this->model->get_authors_with_pseudonyms($params['offset'], $params['limit']);
	}

	function _search_genre($params)
	{
		$this->load->model('genre_model', 'model');
		$genres = $this->model->order_by('lineage', 'asc')->as_array()->get_all();  //->limit($params['limit'], $params['offset'])

		foreach ($genres as $key => $genre)
		{
			$genres[$key]['full_path'] = $this->model->get_full_name_string($genre['id'], $separator = ' > ');
		}

		return $genres;
	}

	function _search_language($params)
	{
		$this->load->model('language_model', 'model');
		//look closer at using sort order, but our language manager doesn't handle adding/editing it right now
		// ->limit($params['limit'], $params['offset']) -- prolly temp, want all on one page for now
		return $this->model->order_by('common', 'desc')->order_by('language', 'asc')->as_array()->get_many_by(array('active' => 1));
	}

	function _search_reader($params)
	{
		$this->load->model('project_reader_model', 'model');
		return $this->model->get_all_distinct($params['offset'], $params['limit']);
	}

	function _search_title($params)
	{
		//this may require revisiting to handle compilations differently

		$params['author_id'] = 0;  //all authors

		$this->load->model('project_model', 'model');

		// use the reduced version for straight title menu searches
		if ($params['primary_key'] == 0)
		{
			$projects = $this->model->get_projects_for_title_menu($params);
		}
		else
		{
			$projects = $this->model->get_projects_by_author($params);
		}

		//echo $this->db->last_query();

		// we'll be changing this query, but for now: the full set query for pagination doesn't need the authors
		if ($params['limit'] == 1000000)
		{
			return $projects;
		}

		$this->load->model('author_model');
		$this->load->model('section_model');

		// We'll decide long-term whether to
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

}

/* End of file controllername.php */
/* Location: ./application/controllers/controllername.php */
