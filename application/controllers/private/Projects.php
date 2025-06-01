<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Projects extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->template->add_css('css/libs/datatable.css');
		$this->template->add_js('js/libs/jquery.dataTables.js');
		$this->template->add_js('js/libs/jquery.jeditable.js');

		$this->load->model('project_model');
	}

	public function index($user_projects = false)
	{
		$params['project_search'] = $this->input->post('project_search', true);
		$this->data['user_projects'] = $params['user_projects'] = $user_projects;

		$this->data['project_search'] = empty($params['project_search']) ? '' : $params['project_search'];

		$this->data['page_title'] = 'Projects';
		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		//load volunteers AFTER the menu_header
		$this->data['projects'] = $this->project_model->get_projects($params);

		$this->load->config('librivox');
		$this->data['project_statuses'] = $this->config->item('project_statuses');

		$this->data['view_validator'] = false;
		$this->data['view_author_manager'] = false;
		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
		if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			$this->data['view_validator'] = true;
			$this->data['view_author_manager'] = true;
		}

		if (!empty($this->data['projects']))
		{
			foreach ($this->data['projects'] as $key => $project)
			{
				$this->data['projects'][$key]->status_group = (in_array($project->status, array('complete', 'on_hold', 'abandoned'))) ? '0' : '1';

				$apps = $this->project_model->get_project_authors($project->id);
				$authors = __()->pluck($apps, 'author');
				$this->data['projects'][$key]->author = implode(', ', $authors);

				$this->data['projects'][$key]->view_compiler = false;
				// only these statuses  can see the section compiler
				$allowed_statuses = array(PROJECT_STATUS_OPEN, PROJECT_STATUS_FULLY_SUBSCRIBED, PROJECT_STATUS_PROOF_LISTENING);
				if (in_array($project->status, $allowed_statuses) || $this->data['view_validator'])
				{
					$this->data['projects'][$key]->view_compiler = true;
				}
			}
		}

		$this->insertMethodCSS();
		$this->insertMethodJS();

		$this->_render($this->base_path . '/' . build_view_path(__METHOD__), $this->data);
	}

	public function ajax_search_catalog()
	{
		if (!$this->input->is_ajax_request())
		{
			echo "Oi! Stop trying to hack us. We're a non-profit, don'tcha know?";
			return false; //we should log attempt
		}

		$this->load->model('project_model');

		$fields = $this->input->post(null, true);

		if ($results = $this->project_model->search($fields))
		{
			if (count($results) == 1)
			{
				//keyword list, comma-delimited
				$results[0]['list_keywords'] = $this->project_model->get_keywords_by_project($results[0]['id']);

				//yes..it does build a string from array, then explode it...yes, you may refactor, oh Ye of Great Amounts of Time
				$results[0]['genres'] = $this->project_model->get_genres_by_project($results[0]['id'], 'id');

				if (!empty($results[0]['genres']))
				{
					$this->load->helper('previewer_helper');

					$genres_array = explode(',', $results[0]['genres']);
					foreach ($genres_array as $key => $genre)
					{
						$results[0]['genre_strings'][] = build_genre_element($genre);
					}
				}

				$counter = 0;  // Important! This is used for two sets of author data
				$author_list = $this->project_model->get_authors_by_project($results[0]['id'], 'author');

				$results[0]['authors'] = array();
				$results[0]['author_views'] = array();

				if (!empty($author_list))
				{
					foreach ($author_list as $author)
					{
						$author['counter'] = $counter++;  //$author['counter'] = $author['id'];
						$results[0]['authors'][] = $author;
						$results[0]['author_views'][] = $this->load->view('public/project_launch/author_block', $author, true);
					}
				}

				$counter = 0;  // Important! This is used for two sets of author data
				$translators_list = $this->project_model->get_authors_by_project($results[0]['id'], 'translator');

				$results[0]['translators'] = array();
				$results[0]['translator_views'] = array();

				if (!empty($translators_list))
				{
					foreach ($translators_list as $translator)
					{
						$translator['counter'] = $counter++;
						$results[0]['translators'][] = $translator;
						$results[0]['translator_views'][] = $this->load->view('public/project_launch/translator_block', $translator, true);
					}
				}

				//get current value of volunteer dropdowns
				$volunteers = array('bc', 'altbc', 'mc', 'pl');
				$this->load->model('user_model');

				foreach ($volunteers as $key => $type)
				{
					$volunteer_array[$type] = array();

					if (!empty($results[0]['person_' . $type . '_id']))
					{
						$user = $this->user_model->get($results[0]['person_' . $type . '_id']);
						if (!empty($user))
						{
							$volunteer_array[$type] = array('user_id' => $user->id, 'username' => $user->username);
						}
					}
					unset($user);
				}

				$results[0]['volunteers'] = $volunteer_array;
			}

			$this->ajax_output($results, TRUE, FALSE);
		}
		$this->ajax_output(array('error' => true, 'msg' => 'No projects found'), FALSE, FALSE);
	}

	function update_project()
	{
		//check permissions -- not needed here

		//update project
		$fields = $this->input->post(null, true);

		if (empty($fields['id'])) $this->ajax_output(array('message' => 'Requires project id'), false);

		if (isset($fields['status']) && $fields['status'] == 'complete')
		{
			$today = new DateTime();
			$fields['date_catalog'] = $today->format('Y-m-d');
		}
		if (isset($fields['status']) && $fields['status'] != 'complete')
		{
			$fields['date_catalog'] = '0000-00-00';
		}

		$this->project_model->update($fields['id'], $fields);

		$this->ajax_output(array('message' => 'Updated project'), true);
	}
}

/* End of file projects.php */
/* Location: ./application/controllers/projects.php */
