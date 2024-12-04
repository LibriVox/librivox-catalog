<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project_launch extends Public_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');
		$this->load->helper('previewer');

		// load generic template stuff
		$this->loadGenericAssets();
	}

	public function index()
	{
		if ($_POST)
		{
			$fields = $this->input->post(null, true);

			//var_dump($fields);return;

			$this->load->helper('string');
			$fields['project_code'] = random_string('alnum', 8);

			if ($this->_validate_form())
			{
				$this->load->library('form_generator_item');
				$altered_fields = $this->form_generator_item->alter_data($fields);

				//we're getting out of here...
				$this->result_page($altered_fields);
				return;
			}
			else
			{
			}
		}

		$this->load->helper('form_helper');

		$this->load->config('librivox');
		$this->data['languages'] = $this->config->item('languages');

		$this->data['page_title'] = 'Project template generator';

		$this->load->model('language_model');
		$this->data['recorded_languages'] = full_languages_dropdown('recorded_language');
		//$this->data['recorded_languages'] = $this->language_model->dropdown('id', 'language');   //as_array()->get_all();

		$this->data['project_types'] = create_array_from_lang('proj_launch_project_', $this->lang->language);

		$this->data['months'] = array(0 => '--') + create_array_from_lang('proj_launch_month_', $this->lang->language);

		$this->data['proof_level'] = create_array_from_lang('proj_launch_proof_level_', $this->lang->language);

		// genres
		//$check_string = 'proj_launch_genre_';
		//$this->data['genres']  = create_array_from_lang($check_string, $array_keys, true);
		$config['table'] = 'genres';
		$this->load->library('mahana_hierarchy', $config);
		$this->data['genres'] = $this->mahana_hierarchy->get_grouped_children();

		$this->data['years'] = array(0 => '--') + years();
		$this->data['days'] = array(0 => '--') + days();

		$this->data['current_lang'] = $this->session->userdata('lang_code');

		$this->insertMethodJS();

		$this->template->add_js('js/common/jquery.tagsinput.min.js');
		$this->template->add_css('css/common/jquery.tagsinput.css');

		$this->template->write_view('head', 'common/workflow_head.php', $this->data);
		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $this->data);
		$this->template->render();
	}

	public function result_page($fields)
	{
		$data = $fields;

		// authors & translators now handled differently - we have multis

		$author_list = array();
		$new_author_list = array();

		if (!empty($fields['author_list']))
		{
			$this->load->model('author_model');
			$author_list = $this->author_model->get_author_list($fields['author_list']);
		}

		if (!empty($fields['new_author_list']))
		{
			$this->load->model('form_generators_authors_model');
			$new_author_list = $this->form_generators_authors_model->get_author_list($fields['new_author_list']);
		}

		$data['author'] = array_merge($author_list, $new_author_list);
		$data['author_filename'] = array();
		$data['notice'] = array();

		foreach ($data['author'] as $author)
		{
			$data['author_filename'][] = format_filename($author['last_name']);
			$data['notice'][] = copyright_notice($author);
		}

		//**** do it all again for translators ***//

		$trans_list = array();
		$new_trans_list = array();

		if (!empty($fields['trans_list']))
		{
			$this->load->model('author_model');
			$trans_list = $this->author_model->get_author_list($fields['trans_list']);
		}

		if (!empty($fields['new_trans_list']))
		{
			$this->load->model('form_generators_authors_model');
			$new_trans_list = $this->form_generators_authors_model->get_author_list($fields['new_trans_list']);
		}

		$data['translator'] = array_merge($trans_list, $new_trans_list);

		foreach ($data['translator'] as $translator)
		{
			$data['notice'][] = copyright_notice($translator);
		}

		//***  continue

		$data['summaryauthor'] = summary_author($fields);

		$data['titlelc'] = clean_title($fields['title']);
		$data['title_filename'] = format_filename($data['titlelc']);

		$data['date'] = concat_date($fields['expected_completion_year'], $fields['expected_completion_month'], $fields['expected_completion_day']);

		$this->load->model('genre_model');
		$data['genres'] = $this->genre_model->convert_ids_to_name($fields['genres']);

		$this->load->library('keywords');
		$data['keyword'] = $this->keywords->get_string($fields['list_keywords']);

		$data['project_img_url'] = IMG_PATH_RESULTS_LOGIN;

		$data['page_title'] = 'Template results page';

		$data['has_preface'] = ($fields['has_preface']) ? lang('proj_launch_yes') : lang('proj_launch_no');

		$data['forum_link'] = '<a href="https://forum.librivox.org" style="margin-right:10px;">LibriVox Forum</a>';
		$data['help_link'] = '<a href="' . base_url() . 'pages/workflow-help">Help</a>';

		$this->insertMethodJS();

		$this->{$fields['project_type'] . '_work'}($data);

		$this->template->write_view('head', 'common/workflow_head.php', $data);
		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $data);
		$this->template->render();
	}

	public function solo_work(&$data)
	{
		$data['url'] = $data['title_filename'] . '_##_' . $data['author_filename'][0] . '_128kb.mp3 ' .
			lang('project_launch_template_section_number') . ' (e.g. ' . $data['title_filename'] .
			'_01_' . $data['author_filename'][0] . '_128kb.mp3)';
	}

	public function collaborative_work(&$data)
	{
		$this->solo_work($data);
	}

	public function dramatic_work(&$data)
	{
		$data['url'] = array();
		$data['url'][0] = $data['title_filename'] . '_[i]role[/i]_#.mp3 ' . lang('project_launch_template_act_number') .
			' (e.g. ' . $data['title_filename'] . '_romeo_1.mp3)';
		$data['url'][1] = $data['title_filename'] . '_#_' . $data['author_filename'][0] . '_128kb.mp3 ' .
			lang('project_launch_template_act_number') . ' (e.g. ' . $data['title_filename'] .
			'_1_' . $data['author_filename'][0] . '_128kb.mp3)';
	}

	public function poetry_weekly_work(&$data)
	{
		$data['url'] = $data['title_filename'] . "_" . $data['author_filename'][0] . '_[i]your initials in lowercase[/i]_128kb.mp3 (e.g. ' .
			$data['title_filename'] . '_' . $data['author_filename'][0] . '_klh_128kb.mp3)';

		// Note: The following code would ideally generate the date based on the
		// currently selected language. However this is not a simple task and
		// currently the poetry templates are only being used for English anyway.
		$data['date'] = concat_date($data['expected_completion_year'], $data['expected_completion_month'],
			$data['expected_completion_day'], 'l, F jS') ?: '[date]';
	}

	public function poetry_fortnightly_work(&$data)
	{
		$this->poetry_weekly_work($data);
	}

	private function _validate_form()
	{
		$this->form_validation->set_rules('lang_select', 'Language Selector', 'trim|required|xss_clean');

		$this->form_validation->set_rules('auth_id[]', 'Author id', 'trim|xss_clean');
		$this->form_validation->set_rules('auth_first_name[]', 'lang:proj_launch_auth_first_name', 'trim|xss_clean|max_length[255]');
		$this->form_validation->set_rules('auth_last_name[]', 'lang:proj_launch_auth_last_name', 'trim|xss_clean|max_length[255]');
		$this->form_validation->set_rules('auth_yob[]', 'lang:proj_launch_auth_dob', 'trim|xss_clean|max_length[255]');
		$this->form_validation->set_rules('auth_yod[]', 'lang:proj_launch_auth_dod', 'trim|xss_clean|max_length[255]');
		$this->form_validation->set_rules('link_to_auth[]', 'lang:proj_launch_link_to_auth', 'trim|xss_clean|prep_url|max_length[255]');

		$this->form_validation->set_rules('trans_id[]', 'Translator id', 'trim|xss_clean|numeric');
		$this->form_validation->set_rules('trans_first_name[]', 'lang:proj_launch_auth_first_name', 'trim|xss_clean');
		$this->form_validation->set_rules('trans_last_name[]', 'lang:proj_launch_auth_last_name', 'trim|xss_clean');
		$this->form_validation->set_rules('trans_yob[]', 'lang:proj_launch_trans_dob', 'trim|xss_clean');
		$this->form_validation->set_rules('trans_yod[]', 'lang:proj_launch_trans_dod', 'trim|xss_clean');
		$this->form_validation->set_rules('link_to_trans[]', 'lang:proj_launch_link_to_trans', 'trim|xss_clean');

		$this->form_validation->set_rules('project_type', 'lang:proj_launch_type_of_project', 'trim|xss_clean|alpha_dash');
		$this->form_validation->set_rules('recorded_language', 'lang:proj_launch_recorded_language', 'trim|xss_clean|numeric');
		$this->form_validation->set_rules('title', 'lang:proj_launch_title', 'trim|xss_clean|required');
		$this->form_validation->set_rules('brief_summary', 'Brief Summary', 'trim|xss_clean');
		$this->form_validation->set_rules('brief_summary_by', 'Brief Summary By', 'trim|xss_clean');
		$this->form_validation->set_rules('link_to_text', 'lang:proj_launch_link_to_text_1', 'trim|xss_clean|prep_url');

		$this->form_validation->set_rules('link_to_book', 'lang:proj_launch_link_to_book', 'trim|xss_clean|prep_url');
		$this->form_validation->set_rules('pub_year', 'lang:proj_launch_pub_year', 'trim|xss_clean');

		$this->form_validation->set_rules('expected_completion_year', 'lang:proj_launch_expected_completion', 'trim|xss_clean');
		$this->form_validation->set_rules('expected_completion_month', 'lang:proj_launch_expected_completion', 'trim|xss_clean');
		$this->form_validation->set_rules('expected_completion_day', 'lang:proj_launch_expected_completion', 'trim|xss_clean');

		$this->form_validation->set_rules('proof_level', 'lang:proj_launch_proof_level', 'trim|xss_clean|alpha_dash');
		$this->form_validation->set_rules('num_sections', 'lang:proj_launch_num_sections', 'trim|xss_clean');

		$this->form_validation->set_rules('has_preface', 'lang:proj_launch_has_preface', 'trim|xss_clean|exact_length[1]');

		$this->form_validation->set_rules('genres', 'Genres', 'xss_clean');

		$this->form_validation->set_rules('list_keywords', 'Keywords', 'trim|xss_clean');

		$this->form_validation->set_rules('soloist_name', 'lang:proj_launch_soloist_name', 'trim|xss_clean');
		$this->form_validation->set_rules('soloist_link', 'Soloist url', 'trim|xss_clean|prep_url');

		return $this->form_validation->run();
	}

	// TESTING FUNCTIONS

	function test_post()
	{
		$fields['lang_select'] = "english";
		$fields['auth_first_name'] = "Joe";
		$fields['auth_last_name'] = "Schmoe";
		$fields['auth_yob'] = "1900";
		$fields['auth_yod'] = "1950";
		$fields['trans_name'] = "George E. Porgie";
		$fields['trans_yob'] = "1880";
		$fields['trans_yod'] = "1947";
		$fields['project_type'] = 'collaborative';//'poetry_fortnightly';  //'poetry_weekly';  //'dramatic';  //'collaborative';  //
		$fields['recorded_language'] = "English";
		$fields['title'] = "Test of the Valkyrie";
		$fields['brief_summary'] = "A simple test of the evil Valkyrie. Will they pass?";
		$fields['brief_summary_by'] = "jmadsen";
		$fields['link_to_text'] = "http://www.gutenberg.org/ebooks/40108";
		$fields['link_to_auth'] = "http://en.wikipedia.org/wiki/Joe_Schmoe";
		$fields['link_to_book'] = "http://en.wikipedia.org/wiki/Test_of_the_Valkyrie";
		$fields['pub_year'] = "1932";
		$fields['expected_completion_year'] = "2013";
		$fields['expected_completion_month'] = "1";
		$fields['expected_completion_day'] = "1";
		$fields['proof_level'] = "Word Perfect";
		$fields['num_sections'] = "6";
		$fields['has_preface'] = "0";
		$fields['genres'] = array("adventure", "comedy", "fiction");
		$fields['list_keywords'] = "adventure;Valkyrie;testing";
		$fields['soloist_name'] = "jmadsen";
		$fields['soloist_link'] = "http://www.codebyjeff.com";

		//in main function
		$fields['project_code'] = random_string('alnum', 8);
		$fields['genres'] = (empty($fields['genres'])) ? '' : implode(',', $fields['genres']);

		$this->result_page($fields);
	}

	function test_title()
	{
		$title_remove = $this->config->item('title_remove');

		foreach ($title_remove as $key => $value)
		{
			$fields['title'] = $value . ' big brown horse ' . $value;
			echo clean_title($fields) . '<br />';
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
