<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Catalog_controller extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('links_helper');
		$this->load->helper('previewer_helper');

		$this->data = array();
	}

	function _render($file)
	{
		$this->data['header'] = $this->load->view('catalog/partials/header', $this->data, true);
		$this->data['sidebar'] = $this->load->view('catalog/partials/sidebar', $this->data, true);
		$this->data['book_sidebar'] = $this->load->view('catalog/partials/book_sidebar', $this->data, true);
		$this->data['footer'] = $this->load->view('catalog/partials/footer', $this->data, true);
		$this->data['advanced_search'] = $this->load->view('catalog/partials/advanced_search', $this->data, true);

		$this->load->view($file, $this->data);
	}

	function _format_pagination($first_page, $page_count, $call_function = 'get_results')
	{
		$pages = $this->_build_pagination_array($first_page, $page_count);

		$html = '<a class="page-number first" data-page_number="1" href="#" data-call_function="' . $call_function . '">&laquo;</a>';

		foreach ($pages as $key => $page)
		{
			//$page_number_active = ($page == $first_page)? 'style="background-color:#FFF;"' : '';
			$active = ($page == $first_page) ? 'active' : '';
			$html .= '<a class="page-number ' . $active . '"  data-page_number="' . $page . '" href="#" data-call_function="' . $call_function . '">' . $page . '</a>';
		}

		$html .= '<a class="page-number last" data-page_number="' . $page_count . '" data-call_function="' . $call_function . '" href="#">&raquo;</a>';

		return $html;
	}

	function _build_pagination_array($first_page, $page_count)
	{
		//need an array of 4 + $first_page + 4
		$num_links = 4;
		$start = max(($first_page - $num_links), 1);
		$end = min(($first_page + $num_links), max($page_count, 1));
		return range($start, $end);
	}

	function _format_results($results, $view = 'author')
	{
		if (empty($results)) return 'No results';

		$html = '';
		foreach ($results as $item)
		{
			$html .= $this->_format_blade($item, $view);
		}

		return $html;
	}

	function _format_blade($item, $view)
	{
		return $this->load->view('catalog/item_blades/' . $view . '_blade', array('item' => $item), TRUE);
	}

	function _author_list($project_id)
	{
		$authors = $this->project_model->get_project_authors($project_id);
		return format_authors($authors, FMT_AUTH_YEARS|FMT_AUTH_HTML|FMT_AUTH_LINK, 2);
	}

	function _authors_string($authors)
	{
		return format_authors($authors, FMT_AUTH_YEARS|FMT_AUTH_HTML|FMT_AUTH_LINK, 2);
	}

	function _authors_string_no_link($authors)
	{
		return format_authors($authors, FMT_AUTH_YEARS, 2);
	}

	function _get_project($slug)
	{
		if (is_numeric($slug))
		{
			$project = $this->project_model->get($slug);
		}

		if (empty($project))
		{
			//our data is all hard-coded...
			$url = 'https://librivox.org/' . $slug;

			$sql = "SELECT * FROM (`projects`) WHERE `url_librivox` = '" . rtrim($url, "/") . "' OR `url_librivox` = '" . rtrim($url) . "/'";

			$project = $this->db->query($sql)->row();

			// this is a crutch because the url domains are all hard-coded into the db values - we'll need to remove this before production!
			if (base_url() == 'http://dev.librivox.org/' && empty($project))
			{
				$url = 'http://dev.librivox.org/' . $slug;
				$sql = "SELECT * FROM (`projects`) WHERE `url_librivox` = '" . rtrim($url, "/") . "' OR `url_librivox` = '" . rtrim($url) . "/'";
				$project = $this->db->query($sql)->row();
			}

			// There is a bug(?) causing _ in slug to be backslashed when using like() -- just write the query above
			//$project = $this->db->like('url_librivox', rtrim($slug,"/"), 'after')->get('projects')->row();
			//$project = $this->db->where('url_librivox', rtrim($slug,"/"))->get('projects')->row();
		}

		if (empty($project))
		{
			$project = false;
		}

		// check status & if user logged in
		if (isset($project->status) && $project->status != PROJECT_STATUS_COMPLETE)
		{
			// first, if logged in, they are trying to preview so show them
			if (!$this->ion_auth->logged_in())
			{
				if (!empty($project->url_forum))
				{
					redirect($project->url_forum);
				}

				$project = false;
			}
		}

		return $project;
	}
}
