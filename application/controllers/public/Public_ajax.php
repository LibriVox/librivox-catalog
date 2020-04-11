<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Public_ajax extends Public_Controller
{

	function language_switcher()
	{
		$this->lang_select();
	}

	function autocomplete_author()
	{
		$term = $this->input->post('term');
		$search_field = $this->input->post('search_field');
		$this->load->model('author_model');
		echo json_encode($this->author_model->autocomplete($term, $search_field));
	}

	function autocomplete_user()
	{
		$term = $this->input->post('term');
		$search_field = $this->input->post('search_field');
		$this->load->model('user_model');
		echo json_encode($this->user_model->autocomplete($term, $search_field));
	}

	function __add_author()
	{
		$fields = $this->input->post(null, true);

		$this->load->model('Form_generators_authors_model');
		$auth_id = $this->Form_generators_authors_model->insert($fields);

		$data = array('auth_id' => $auth_id);
		echo json_encode($data);
	}

	function add_author()
	{
		$data['counter'] = $this->input->post('counter', true);

		//get next counter
		$data['counter']++;

		$html = $this->load->view('public/project_launch/author_block', $data, TRUE);
		$data = array('html' => $html);
		echo json_encode($data);
	}

	function add_translator()
	{
		$data['counter'] = $this->input->post('counter', true);

		//get next counter
		$data['counter']++;

		$html = $this->load->view('public/project_launch/translator_block', $data, TRUE);
		$data = array('html' => $html);
		echo json_encode($data);
	}
}
