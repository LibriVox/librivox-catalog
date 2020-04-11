<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Administer_projects extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->library('Catalog_item');
		$this->load->helper('previewer');

		//check permissions
		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
		if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			if ($this->input->is_ajax_request())
			{
				echo "You have no access permissions to this screen";
				return false; //we should log attempt
			}

			redirect('auth/error_no_permission');
		}
	}

	public function index()
	{
		$prototype['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $prototype);
		$this->template->render();
	}

	public function add_catalog_item($project_id = 0)
	{
		//We might be new here, we might be looking up the project launch data, or we might be submitting our new data

		$prototype = $this->catalog_item->get_prototype();

		$prototype['usage'] = is_numeric($project_id) ? 'project_id' : $project_id;
		$prototype['project_id'] = is_numeric($project_id) ? $project_id : 0;

		$prototype['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		// to add extra urls - this should not be on the project_launch form
		$prototype['project_url_modal'] = $this->load->view('private/administer_projects/project_urls_modal', $this->data, TRUE);

		// project's section reader modal
		$prototype['project_readers_modal'] = $this->load->view('private/administer_projects/project_readers_modal', $this->data, TRUE);

		$this->load->model('user_model');

		/*
		   We make dropdown of available volunteers (bc, mc, etc) However, the person who was the actual
		   volunteer on the project may not still be volunteering (and so not have the role or appear on the
		   dropdown). So we add them on manually
		*/
		if ($project_id)
		{
			$this->load->model('project_model');
			$project = $this->project_model->get($project_id);

			$volunteers = array('bc', 'altbc', 'mc', 'pl');

			foreach ($volunteers as $key => $type)
			{
				$volunteer_array[$type] = array();

				if (!empty($project->{'person_' . $type . '_id'}))
				{
					$user = $this->user_model->get($project->{'person_' . $type . '_id'});
					if (!empty($user))
					{
						$volunteer_array[$type] = array($user->id => $user->username);
					}
				}
				unset($user);
			}
		}

		$prototype['bcs'] = $this->user_model->get_dropdown_by_role('bc', true, true) + $volunteer_array['bc'];
		$prototype['altbcs'] = $this->user_model->get_dropdown_by_role('bc', true, true) + $volunteer_array['altbc'];
		$prototype['mcs'] = $this->user_model->get_dropdown_by_role('mc', true, true) + $volunteer_array['mc'];
		$prototype['pls'] = $this->user_model->get_dropdown_by_role('pl', true, true) + $volunteer_array['pl'];

		if ($fields = $this->input->post(null, true))
		{
			$this->load->model('form_generator_model');
		}

		//var_dump($prototype);
		$this->load->model('language_model');
		$prototype['recorded_languages'] = full_languages_dropdown('recorded_language');// $this->language_model->dropdown('id', 'language');   //as_array()->get_all();

		$prototype['statuses'] = $this->config->item('project_statuses');

		$prototype['page'] = 'new_project_form';
		$prototype['project_search_form'] = $this->load->view('private/common/project_search', $prototype, true);

		//genres
		$config['table'] = 'genres';
		$this->load->library('mahana_hierarchy', $config);
		$prototype['genres'] = $this->mahana_hierarchy->get_grouped_children();

		$prototype['project_types'] = create_array_from_lang('proj_launch_project_', $this->lang->language);

		$this->insertMethodCSS();
		$this->insertMethodJS();

		$this->template->add_js('js/common/jquery.tagsinput.min.js');
		$this->template->add_js('js/libs/moment.min.js');
		$this->template->add_css('css/common/jquery.tagsinput.css');
		$this->template->add_css('css/private/administer_projects/new_project_form.css');
		$this->template->add_js('js/private/administer_projects/new_project_form.js');

		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $prototype);
		$this->template->render();
	}

	public function ajax_lookup_project_code()
	{
		if (!$this->input->is_ajax_request())
		{
			echo "Oi! Stop trying to hack us. We're a non-profit, don'tcha know?";
			return false; //we should log attempt
		}

		$prototype = $this->catalog_item->get_prototype();

		if ($project_code = $this->input->post('project_code', true))
		{
			$this->load->model('form_generator_model');

			if ($project_launch_data = $this->form_generator_model->get_by('project_code', $project_code))
			{
				//var_dump($project_launch_data);
				$this->load->helper('previewer');
				$this->load->library('keywords');

				$prototype['title_prefix'] = clean_title($project_launch_data->title, 'prefix');
				$prototype['projectname'] = clean_title($project_launch_data->title, 'title');
				$prototype['projectdescription'] = clean_summary($project_launch_data);
				$prototype['type'] = $project_launch_data->project_type;
				$prototype['is_compilation'] = $project_launch_data->is_compilation;
				$prototype['nsections'] = $project_launch_data->num_sections;
				$prototype['firstsection'] = ($project_launch_data->has_preface) ? 0 : 1;
				$prototype['begindate'] = date('Y-m-d');
				$prototype['status'] = 'open';

				$prototype['targetdate'] = concat_date($project_launch_data->expected_completion_year, $project_launch_data->expected_completion_month, $project_launch_data->expected_completion_day);
				if ($project_launch_data->expected_completion_year == 0 || $project_launch_data->expected_completion_month == 0 || $project_launch_data->expected_completion_day == 0)
				{
					$prototype['targetdate'] = '';
				}

				$prototype['project_type'] = $project_launch_data->project_type;
				$prototype['copyrightyear'] = $project_launch_data->edition_year;
				$prototype['copyrightcheck'] = false;
				$prototype['gutenburgurl'] = $project_launch_data->link_to_text;
				$prototype['wikibookurl'] = $project_launch_data->link_to_book;
				$prototype['recorded_language'] = $project_launch_data->recorded_language;
				$prototype['list_keywords'] = $this->keywords->get_string($project_launch_data->list_keywords);

				//$prototype['notes']        = $project_launch_data->notes;

				// genres - blocks and hidden value
				$prototype['genres'] = $project_launch_data->genres;

				if (!empty($project_launch_data->genres))
				{
					$genres_array = explode(',', $project_launch_data->genres);
					foreach ($genres_array as $key => $genre)
					{
						$prototype['genre_strings'][] = build_genre_element($genre);
					}
				}

				$counter = 0;  // Important! This is used for two sets of author data
				if (!empty($project_launch_data->author_list))
				{
					$this->load->model('author_model');
					$author_list = $this->author_model->get_author_list($project_launch_data->author_list);

					foreach ($author_list as $author)
					{
						$author['counter'] = $counter++;
						$prototype['authors'][] = $author;
						$prototype['author_views'][] = $this->load->view('public/project_launch/author_block', $author, true);
					}
				}

				if (!empty($project_launch_data->new_author_list))
				{
					$this->load->model('form_generators_authors_model');
					$new_author_list = $this->form_generators_authors_model->get_author_list($project_launch_data->new_author_list);

					foreach ($new_author_list as $author)
					{
						$author['counter'] = $counter++;

						$author_suggestion_list = $this->author_model->get_author_suggestions($author['first_name'], $author['last_name']);

						foreach ($author_suggestion_list as $key => $author_suggestion)
						{
							$author['suggestion'][] = $author_suggestion;
						}

						$author['id'] = 0;  //we want to set these to 0 to tell the next form to insert
						$prototype['authors'][] = $author;
						$prototype['author_views'][] = $this->load->view('public/project_launch/author_block', $author, true);
					}
				}

				//*** now do this for translators

				$counter = 0;  // Important! This is used for two sets of author data
				if (!empty($project_launch_data->trans_list))
				{
					$this->load->model('author_model');
					$translator_list = $this->author_model->get_author_list($project_launch_data->trans_list);

					foreach ($translator_list as $translator)
					{
						$translator['counter'] = $counter++;
						$prototype['translators'][] = $translator;
						$prototype['translator_views'][] = $this->load->view('public/project_launch/translator_block', $translator, true);
					}
				}

				if (!empty($project_launch_data->new_trans_list))
				{
					$this->load->model('form_generators_authors_model');
					$new_translator_list = $this->form_generators_authors_model->get_author_list($project_launch_data->new_trans_list);

					foreach ($new_translator_list as $translator)
					{
						$translator['counter'] = $counter++;

						$translator_suggestion_list = $this->author_model->get_author_suggestions($translator['first_name'], $translator['last_name']);

						foreach ($translator_suggestion_list as $key => $translator_suggestion)
						{
							$translator['suggestion'][] = $translator_suggestion;
						}

						$translator['id'] = 0;  //we want to set these to 0 to tell the next form to insert
						$prototype['translators'][] = $translator;
						$prototype['translator_views'][] = $this->load->view('public/project_launch/translator_block', $translator, true);
					}
				}

				$this->ajax_output($prototype, TRUE, FALSE);
			}
			else
			{
				$this->ajax_output(array('error' => true, 'msg' => 'No project found'), FALSE, FALSE);
			}
		}

		$this->ajax_output(array('error' => true, 'msg' => 'No project code supplied'), FALSE, FALSE);
	}

	public function ajax_add_catalog_item()
	{
		if (!$this->input->is_ajax_request())
		{
			echo "Oi! Stop trying to hack us. We're a non-profit, don'tcha know?";
			return false; //we should log attempt
		}

		$this->form_validation->set_rules('projectname', 'Title', 'trim|required|xss_clean');
		if ($this->form_validation->run() === false)
		{
			$this->ajax_output(array('error' => true, 'msg' => 'The title is required'), FALSE, FALSE);
			return;
		}

		$fields = $this->input->post(null, TRUE);
		$result = $this->catalog_item->alter_data($fields);

		//'sql'=>$this->db->last_query(),
		$this->ajax_output($result, TRUE, FALSE);
		//$this->ajax_output(array('error'=>true, 'msg'=>'There was an issue adding/updating your record. Please try again.') , FALSE , FALSE);

	}

	public function ajax_get_reader_list()
	{
		$fields = $this->input->post(null, TRUE);

		$this->load->model('section_model');
		$sections = $this->section_model->get_full_sections_info($fields['project_id']);

		// re-jigger our data a bit for the table
		foreach ($sections as $key => $section)
		{
			foreach ($section->readers as $section_key => $reader)
			{
				$results[] = array('section_number' => $section->section_number,
					'title' => $section->title,
					'reader_name' => $reader->reader_name,
					'reader_id' => $reader->reader_id,
					'display_name' => $reader->display_name);
			}
		}

		$this->ajax_output(array('sections' => $results), TRUE, FALSE);
	}

	// Project url functions

	function update_add_project_url()
	{
		$fields = $this->input->post(null, true);

		$this->load->model('project_url_model');

		if ($fields['id'])
		{
			$this->project_url_model->update($fields['id'], $fields);
			$message = 'Updated!';
			$type = 'update';
		}
		else
		{
			unset($fields['id']);
			$fields['id'] = $this->project_url_model->insert($fields);
			$message = 'Added!';
			$type = 'insert';
		}

		$this->ajax_output(array('message' => $message, 'project_url_id' => $fields['id'], 'type' => $type), TRUE, FALSE);
	}

	public function get_project_urls()
	{
		$fields = $this->input->post(null, true);

		$this->load->model('project_url_model');

		if ($fields['project_id'])
		{
			$project_urls = $this->project_url_model->get_many_by(array('project_id' => $fields['project_id']));
			$this->ajax_output(array('project_urls' => $project_urls), TRUE, FALSE);
		}

		$this->ajax_output(array('message' => 'An error occurred'), FALSE, FALSE);
	}

	public function delete_project_url()
	{
		$fields = $this->input->post(null, true);

		$this->load->model('project_url_model');

		if ($fields['id'])
		{
			$this->project_url_model->delete($fields['id']);
			$message = 'Removed!';
		}

		$this->ajax_output(array('message' => $message), TRUE, FALSE);
	}
}

/* End of file Controllername.php */
/* Location: ./application/controllers/Controllername.php */
