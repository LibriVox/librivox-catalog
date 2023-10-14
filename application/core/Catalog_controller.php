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

	function _format_pagination($current_page, $page_count, $call_function = 'get_results')
	{
        $page_count = intval($page_count); // It's a float, but we want ints. (Causes problems when generating ranges.)

        // If it's less than ten pages, just render everything, otherwise, three either side will do
        $num_links_either_side = $page_count < 10 ? 10 : 3;
		$pages = $this->_build_pagination_array($current_page, $page_count, $num_links_either_side);

        $html = '';

        // Print the link to the first page
        $html .= $this->_format_pagination_link(1, $current_page, $call_function);
        $distance_to_first_page = $pages[0] - 1;
        if ($distance_to_first_page === 2)
        {
            // If we're only one hop away, just print the link rather than the '...'
            $html .= $this->_format_pagination_link(2, $current_page, $call_function);
        }
        else if ($distance_to_first_page > 2)
        {
            $html .= ' ... ';
        }

        // Print the pages either side of the selected page, but not the first or last pages
		foreach ($pages as $page)
		{
            if ($page === 1 || $page === $page_count)
            {
                continue;
            }
            else
            {
                $html .= $this->_format_pagination_link($page, $current_page, $call_function);
            }
		}

        // Print the link to the last page
        $last_page_to_show = $pages[count($pages) - 1];
        $distance_to_last_page = $page_count - $last_page_to_show;
        if ($distance_to_last_page === 2)
        {
            // If we're only one hop away, just print the link rather than the '...'
            $html .= $this->_format_pagination_link($page_count - 1, $current_page, $call_function);
        }
        else if ($distance_to_last_page > 2) {
            $html .= ' ... ';
        }
        $html .= $this->_format_pagination_link($page_count, $current_page, $call_function);

		return $html;
	}

	function _build_pagination_array($first_page, $page_count, $num_links)
	{
		//need an array of (n links) + $first_page + (n links)
		$start = max(($first_page - $num_links), 1);
		$end = min(($first_page + $num_links), max($page_count, 1));
		return range($start, $end);
	}

    function _format_pagination_link($page, $current_page, $call_function)
    {
        $active = ($page == $current_page) ? 'active' : '';
        return '<a
                  class="page-number ' . $active . '"
                  data-page_number="' . $page . '"
                  href="#"
                  data-call_function="' . $call_function . '"
                  >' . $page . '</a>';
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

			$sql = "SELECT * FROM (`projects`) WHERE `url_librivox` = ? OR `url_librivox` = ?";
			$bindings = array(rtrim($url, "/"), rtrim($url) . '/');

			$project = $this->db->query($sql, $bindings)->row();

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
