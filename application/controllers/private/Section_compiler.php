<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Section_compiler extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->template->add_css('css/libs/datatable.css');
		$this->template->add_js('js/libs/jquery.dataTables.js');
		$this->template->add_js('js/libs/jquery.jeditable.js');

		$this->load->helper('previewer_helper');

		$this->load->model('section_model');
	}

	public function index($project_id = 0)
	{
		if (empty($project_id))
		{
			$project_id = $this->input->get_post('project_id', true);
		}

		if (empty($project_id))
		{
			redirect(base_url() . 'private/section_compiler/select_project');
		}

		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		$this->data['project_id'] = $project_id;

		$this->load->model('project_model');
		$this->data['project'] = $this->project_model->get($project_id);

		$this->data['admin_mc'] = 0;

		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
		if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			// only these statuses  can see the section compiler
			$allowed_statuses = array(PROJECT_STATUS_OPEN, PROJECT_STATUS_FULLY_SUBSCRIBED, PROJECT_STATUS_PROOF_LISTENING);
			if (in_array($this->data['project']->status, $allowed_statuses) === false)
			{
				redirect(base_url() . 'private/section_compiler/select_project');
			}

			//if BC or PL, you can only see your own projects
			$own_project = array();
			$own_project[] = $this->data['project']->person_bc_id;
			$own_project[] = $this->data['project']->person_altbc_id;
			$own_project[] = $this->data['project']->person_pl_id;

			if (in_array($this->data['user_id'], $own_project) === false) redirect(base_url() . 'auth/error_no_permission');
		}
		else
		{
			$this->data['admin_mc'] = 1;
		}

		$this->data['sections'] = $this->section_model->get_full_sections_info($project_id);

		$this->load->helper('general_functions_helper');
		$this->data['recorded_languages'] = full_languages_dropdown('add_language_id');

		$this->data['statuses'] = $this->config->item('project_statuses');
		unset($this->data['statuses']['']);  //remove Select One option

		$this->insertMethodCSS();
		$this->insertMethodJS();

		$this->template->add_js('js/common/autocomplete.js');

		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $this->data);
		$this->template->render();
	}

	public function select_project()
	{
		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		$this->data['statuses'] = $this->config->item('project_statuses');

		$this->data['page'] = 'section_compiler';
		$this->data['usage'] = 'search';
		$this->data['project_search_form'] = $this->load->view('private/common/project_search', $this->data, true);

		$this->template->add_css('css/private/administer_projects/new_project_form.css'); //reuse results styling
		$this->insertMethodJS();

		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $this->data);
		$this->template->render();
	}

	public function order_sections()
	{
		$project_id = $this->input->post('project_id', true);
		$sortOrder = $this->input->post('sortOrder', true);

		//we'll update all the sections
		$sort_order_array = json_decode($sortOrder);

		if (!empty($sort_order_array))
		{
			$this->load->model('project_model');
			$project = $this->project_model->get($project_id);

			$counter = abs($project->has_preface - 1);

			foreach ($sort_order_array as $key => $value)
			{
				$this->section_model->update($value, array('section_number' => $counter));
				$counter++;
			}
		}

		$this->ajax_output(array('sortOrder' => $sortOrder), TRUE, FALSE);
	}

	public function update_section_value()
	{
		$id = $this->input->post('id', true);
		$value = $this->input->post('value', true);

		list($field, $section_number) = explode('-', $id);

		$this->section_model->update($section_number, array($field => trim($value)));

		echo $value;
		return;
		//$this->ajax_output(array('value'=>$value) , TRUE , FALSE);

	}

	public function add_section()
	{
		$fields = $this->input->post(null, true);

		$max_section_number = $this->section_model->get_max_section_number($fields['project_id']);

		$fields['section_number'] = $max_section_number + 1;

		$fields['status'] = (empty($fields['reader_id'])) ? 'Open' : 'Assigned';

		$reader_id = $fields['reader_id'];
		unset($fields['reader_id']);

		$section_id = $this->section_model->insert($fields);

		if (!empty($reader_id))
		{
			$this->load->model('section_readers_model');
			$this->section_readers_model->insert(array('section_id' => $section_id, 'reader_id' => $reader_id));
		}

		$this->load->model('project_model');
		$this->project_model->update($fields['project_id'], array('num_sections' => $fields['section_number']));

		$this->ajax_output(array('id' => $section_id, 'section_number' => $fields['section_number'], 'message' => 'Section added successfully'), TRUE, FALSE);
	}

	function delete_section()
	{
		$fields = $this->input->post(null, true);

		//check permissions
		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
		if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			//if BC or PL, you can only see your own projects
			$own_project = array();
			$own_project[] = $this->data['project']->person_bc_id;
			$own_project[] = $this->data['project']->person_altbc_id;
			$own_project[] = $this->data['project']->person_pl_id;

			if (!in_array($this->data['user_id'], $own_project)) redirect(base_url() . 'auth/error_no_permission');
		}

		$this->section_model->delete($fields['section_id']);

		//delete section readers
		$this->load->model('section_readers_model');
		$this->section_readers_model->delete_by(array('section_id' => $fields['section_id']));

		$this->ajax_output(array('message' => 'Section deleted successfully'), TRUE, FALSE);
	}

	/*
		public function add_reader()
		{
			$fields = $this->input->post(null, true);

			$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|is_unique[persons.name]');
			$this->form_validation->set_rules('display_name', 'Display Name', 'trim|required|xss_clean|is_unique[persons.display_name]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|is_unique[persons.email]');

			if ($this->form_validation->run() == FALSE)
				$this->ajax_output(array('message'=>$this->form_validation->error_string()) , FALSE , FALSE);

			$this->load->model('person_model');
			$person_id = $this->person_model->insert($fields);

			$this->ajax_output(array('person_id'=>$person_id) , TRUE , FALSE);

		}
	*/

	// Section meta data
	public function get_meta_data()
	{
		$section_id = $this->input->post('section_id', true);

		$this->load->model('section_model');
		$section = $this->section_model->get($section_id);

		if ($section->author_id)
		{
			$this->load->model('author_model');
			$author = $this->author_model->get($section->author_id);
			$section->author_name = $author->first_name . ' ' . $author->last_name;
		}

		if (!empty($section->playtime)) $section->playtime = format_playtime($section->playtime);

		$this->ajax_output(array('section' => $section), TRUE, FALSE);
	}

	public function add_meta_data()
	{
		$fields = $this->input->post(null, true);

		$fields['playtime'] = time_string_to_secs($fields['playtime']);
        $fields['language_id'] = $fields['language_id'] ? $fields['language_id'] : NULL;

		$this->load->model('section_model');
		$this->section_model->update($fields['id'], $fields);

		$this->ajax_output(array('message' => 'Updated'), TRUE, FALSE);
	}

	public function add_reader_sections()
	{
		$fields = $this->input->post(null, true);

		$section_list = json_decode($fields['section_list']);
		$reader_list = json_decode($fields['reader_id']);

		$this->load->model('section_model');

		//notice this is section NUMBER (the label), not id
		foreach ($section_list as $section_number)
		{
			//
			if (empty($fields['reader_id'])) continue;

			//we allow multiple readers per section in db; we may be assigning multiples as well
			foreach ($reader_list as $reader_id)
			{
				$this->section_model->add_section_reader($fields['project_id'], $section_number, $reader_id);
			}

			//set section status to "assigned" if "open" (only need to do once, not per reader)
			$section = $this->section_model->get_by(array('project_id' => $fields['project_id'], 'section_number' => $section_number));

			if (strtolower($section->status) == 'open')
			{
				$this->section_model->update_by(array('project_id' => $fields['project_id'], 'section_number' => $section_number), array('status' => 'Assigned'));
			}
		}

		$this->ajax_output(array('message' => 'Reader assigned'), TRUE, FALSE);
	}

	public function remove_reader_sections()
	{
		$fields = $this->input->post(null, true);

		$section_list = json_decode($fields['section_list']);
		//notice this is section NUMBER (the label), not id
		foreach ($section_list as $section_number)
		{
			$this->load->model('section_model');
			//$this->section_model->update_by(array('project_id'=>$fields['project_id'], 'section_number'=>$section_number), array('reader_id'=>$fields['reader_id']));

			//we allow multiple readers per section
			$section_id = $this->section_model->remove_section_reader($fields['project_id'], $section_number, $fields['reader_id']);

			//set section status to "open" if no more readers
			$this->load->model('section_readers_model');
			$section_reader_count = $this->section_readers_model->count_by(array('section_id' => $section_id));

			if (strtolower($section_reader_count) == 0)
			{
				$this->section_model->update_by(array('project_id' => $fields['project_id'], 'section_number' => $section_number), array('status' => 'Open'));
			}
		}

		$this->ajax_output(array('message' => 'Reader removed'), TRUE, FALSE);
	}
}

/* End of file section_compiler.php */
/* Location: ./application/controllers/section_compiler.php */
