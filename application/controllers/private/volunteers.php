<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Volunteers extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->template->add_css('css/libs/datatable.css');
		$this->template->add_js('js/libs/jquery.dataTables.js');
		$this->template->add_js('js/libs/jquery.jeditable.js');

		$this->load->model('user_model');
	}

	public function index($user_type = false)
	{
		ini_set('memory_limit', '-1'); //we need to see about chunking this

		$params['user_search'] = $this->input->post('user_search', true);
		$params['user_type'] = $user_type;

		$this->data['search_term'] = empty($params['user_search']) ? '' : $params['user_search'];

		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		//load volunteers AFTER the menu_header
		$this->data['volunteers'] = $this->user_model->get_users($params);

		//check permissions
		$this->data['show_edit'] = false;
		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
		if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			$this->data['show_edit'] = true;
		}

		$this->insertMethodCSS();
		$this->insertMethodJS();

		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $this->data);
		$this->template->render();
	}

	// prolly obsolete now - we use ion_auth add_user()
	public function add_volunteer()
	{
		$fields = $this->input->post(null, true);

		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|is_unique[users.name]');
		$this->form_validation->set_rules('display_name', 'Display Name', 'trim|required|xss_clean|is_unique[users.display_name]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|is_unique[users.email]');

		if ($this->form_validation->run() == FALSE)
			$this->ajax_output(array('message' => $this->form_validation->error_string()), FALSE, FALSE);

		$this->load->model('user_model');
		$user_id = $this->user_model->insert($fields);

		$this->ajax_output(array('user_id' => $user_id), TRUE, FALSE);
	}
}

/* End of file volunteers.php */
/* Location: ./application/controllers/volunteers.php */
