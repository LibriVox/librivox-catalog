<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Page extends Catalog_controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('general_functions_helper');
	}

	public function index($slug)
	{
		$this->load->model('project_model');
		$this->load->model('user_model');

		$this->data['search_category'] = 'page';
		$this->data['primary_key'] = 0;

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
			$this->data['authors'] = array_slice($this->data['authors'], 0, 20); //only show 20 authors on page

			$this->data['authors_string'] = $this->_authors_string($this->data['authors']);
		}

		// **** TRANSLATORS ****//
		$this->data['translators'] = $this->author_model->get_author_list_by_project($this->data['project']->id, 'translator');
		if (!empty($this->data['translators']))
		{
			$this->data['translators'] = array_slice($this->data['translators'], 0, 20); //only show 20 authors on page

			$this->data['authors_string'] .= '<br />Translated by ' . $this->_authors_string($this->data['translators']);
		}

		//var_dump($this->data['project']);

		// **** VOLUNTEERS ****//
		$this->data['volunteers'] = new stdClass();
		$this->data['volunteers']->bc = $this->user_model->get($this->data['project']->person_bc_id);
		$this->data['volunteers']->mc = $this->user_model->get($this->data['project']->person_mc_id);
		$this->data['volunteers']->pl = $this->user_model->get($this->data['project']->person_pl_id);
	
		
		// **** KEYWORDS ****//
		$MAX_KEYWORDS_TO_DISPLAY_INITIALLY = 10;
		$this->data['keywords_array'] = $this->project_model->get_keywords_and_statistics_by_project($this->data['project']->id);
		$this->data['project']->has_long_keywords_list = $this->_has_long_keywords_list($this->data['project']->id, $MAX_KEYWORDS_TO_DISPLAY_INITIALLY);
		$this->data['project']->formatted_keywords_string_primary = $this->_formatted_keywords_string($this->data['project']->id, 
													$this->data['project']->has_long_keywords_list, 
													$MAX_KEYWORDS_TO_DISPLAY_INITIALLY, 
													"primary_list");
		$this->data['project']->formatted_keywords_string_secondary = $this->_formatted_keywords_string($this->data['project']->id, 
													$this->data['project']->has_long_keywords_list, 
													$MAX_KEYWORDS_TO_DISPLAY_INITIALLY, 
													"secondary_list");

		// **** MISC ****//
		$this->data['project']->project_type = (trim($this->data['project']->project_type) == 'solo') ? 'solo' : 'group'; //keep on top - other values dependent on solo/group
		$this->data['project']->read_by = $this->_read_by($this->data['project']);

		$this->data['project']->genre_list = $this->_genre_list($this->data['project']->id);

		$this->data['project']->group = $this->_project_group($this->data['project']->id);

		$this->data['project']->language = $this->_language($this->data['project']->language_id);

		// Build up the links

		$this->load->model('project_url_model');
		$this->data['project']->project_urls = $this->project_url_model->order_by('order', 'asc')->get_many_by(array('project_id' => $this->data['project']->id));

		$this->load->model('section_model');
		//$this->data['sections'] = $this->section_model->as_array()->get_many_by(array('project_id'=>$this->data['project']->id));
		$this->data['sections'] = $this->section_model->get_full_sections_info($this->data['project']->id);

		$this->load->helper('description_html_render');
		$this->data['project']->description = _normalize_and_deduplicate_newlines_in_html($this->data['project']->description);

		//var_dump($this->data['sections']);
		if (!empty($this->data['sections']))
		{
			foreach ($this->data['sections'] as $key => $section)
			{
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

		$this->_render('catalog/page');
		return;
	}

	function _project_group($project_id = 0)
	{
		$this->load->model('group_model');
		$group = $this->group_model->get_group_by_project($project_id);

		return $group;
	}

	function _authors_string($authors)
	{
		return format_authors($authors, FMT_AUTH_YEARS|FMT_AUTH_HTML|FMT_AUTH_LINK, 2);
	}

	function _read_by($project)
	{
		if (strtolower($project->project_type) == 'group') return 'LibriVox Volunteers';

		$readerstub = $this->project_model->get_solo_reader($project->id);

		if (isset($readerstub)) return '<a href="' . base_url() . 'reader/' . $readerstub['reader_id'] . '">' . $readerstub['display_name'] . '</a>';

		return 'Solo';
	}

	function _readers_string($readers)
	{
		if (empty($readers)) return '';

		if (count($readers) > 2) return 'Group';

		$html = '';
		foreach ($readers as $key => $reader)
		{
			$html .= '<a href="' . base_url() . 'reader/' . $reader->reader_id . '">' . $reader->display_name . '</a><br/>';
		}

		return $html;
	}

	function _genre_list($project_id)
	{
		$genre_array = $this->project_model->get_genres_by_project($project_id, 'name', 'array');

		//limit to 3 genres
		$genre_array = array_slice($genre_array, 0, 3);

		if (empty($genre_array)) return '';

		foreach ($genre_array as $key => $genre)
		{
			//$genres[] = '<a href="#" class="js-genre_button" data-genre_id="'.$genre['id'].'">'.$genre['name']. '</a>';
			$genres[] = $genre['name'];
		}

		return implode(', ', $genres);
	}
	
	function _formatted_keywords_string($project_id, $project_has_long_keywords_list, 
						$max_keywords_to_display_initially, $list_variant)
	{
	/** 	The catalog page will contain one, or possibly two lists of keywords.
		It will always contain a primary list.
		If the project has more keywords than can conveniently be fitted in the primary list:
		(a) We truncate this primary list
		(b) We also output to the page a secondary list, iniitially hidden, that includes all the keywords for the project
		(c) We add to the primary list a "Show full list" link which, if clicked, hides the primary list 
		and shows the secondary list
		*/
		
		$return_value = "";
		
		switch ($list_variant) {
  			case "primary_list":
  				if ($project_has_long_keywords_list)
  				{
  					$return_value =	$this->_formatted_truncated_keywords_string($project_id, $max_keywords_to_display_initially);
  				} 
  				else
  				{
  					$return_value =	$this->_formatted_full_keywords_string($project_id); 
  				}
    				break;
  			case "secondary_list";
    				if ($project_has_long_keywords_list)
  				{
  					$return_value =	$this->_formatted_full_keywords_string($project_id);
  				} 
    				break;
  			default:
    				$return_value = "";
		}
		return $return_value;
	}
	
	
	function _formatted_full_keywords_string($project_id) 
	{
		$return_value = '';
		$keywords_array = $this->data['keywords_array'];
		if (!empty($keywords_array))
		{
			foreach ($keywords_array as $key => $row)
			{
			// Add hyperlink only if at least two projects use this keyword
				if ((int)$row['keyword_count'] > 1) 
				{
					$return_value .= '<a href="' . base_url() . 'keywords/' . $row['id'] . '">';
				} 
				$return_value .= $row['value'];
				if ((int)$row['keyword_count'] > 1)
				{
					$return_value .= '</a>';
				} 
				$return_value .= ' (' . $row['keyword_count'] . '), ';
			}
		} 
		else 
		{
			return $return_value;
		}
		// trim off final comma and trailing space 
		$return_value = substr($return_value, 0, -2);
		return $return_value;	
	}
	
	function _formatted_truncated_keywords_string($project_id, $max_keywords_to_display_initially) 
	{
		$return_value = '';
		$keywords_array = $this->data['keywords_array'];
		if (!empty($keywords_array))
		{
			$i = 0;
			foreach ($keywords_array as $key => $row)
			{
				
			// Add hyperlink only if at least two projects use this keyword
				if ((int)$row['keyword_count'] > 1) 
				{
					$return_value .= '<a href="' . base_url() . 'keywords/' . $row['id'] . '">';
				} 
				$return_value .= $row['value'];
				if ((int)$row['keyword_count'] > 1)
				{
					$return_value .= '</a>';
				} 
				$return_value .= ' (' . $row['keyword_count'] . '), ';
				$i++;
				if ($i == $max_keywords_to_display_initially)
				{
					break;
				}
			}
		} 
		else 
		{
			return $return_value;
		}
		// trim off final comma and trailing space 
		$return_value = substr($return_value, 0, -2);
		
		// Add "Show full list" link
		$return_value .= " ... <a href='#' class='truncated-keywords js-truncated-keywords'>[Show full list]</a>";
		
		return $return_value;	
	}
	
	function _has_long_keywords_list($project_id, $max_keywords_to_display_initially)
	{
		$keywords_number = $this->project_model->get_keywords_count_by_project($project_id)->keywords_number;
		return $keywords_number > $max_keywords_to_display_initially;
	}
	
		
	function _language($language_id)
	{
		$this->load->model('language_model');
		return $this->language_model->get($language_id)->language;
	}
}
