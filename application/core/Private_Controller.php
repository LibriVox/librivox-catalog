<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Private_Controller
 * This is where the members, etc is located - it is a base class
 * It DOES require login
 */
class Private_Controller extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->base_path = 'private';

		$this->loadGenericAssets();

		if (!$this->ion_auth->logged_in())
		{
			if ($this->input->is_ajax_request())
			{
				$this->ajax_output(array('code' => 'not_logged_in'), false);
			}

			$this->session->set_flashdata('referrer', current_url());

			redirect('auth/login');
		}

		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_BCS, PERMISSIONS_MCS, PERMISSIONS_PLS);
		if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			redirect('auth/error_no_permission');
		}

		$this->data['user_id'] = $this->librivox_auth->get_user_id();

		$user_groups = $this->librivox_auth->get_users_groups($this->data['user_id'])->result_array();

		//quick fixer upper
		foreach ($user_groups as $key => $role)
		{
			$this->data['user_groups'][] = $role['id'];
		}

		$this->template->add_js('js/private/common/profile_modal.js');

		//need to add only role specific checkboxes
		$roles = $this->config->item('roles');

		$this->roles['roles'][PERMISSIONS_READERS] = array(
			'label' => 'Reader',
			'value' => $roles[PERMISSIONS_READERS],
			'checked' => (in_array($roles[PERMISSIONS_READERS], $this->data['user_groups'])),
			'disabled' => !$this->librivox_auth->in_group(array(PERMISSIONS_ADMIN, PERMISSIONS_MCS, PERMISSIONS_BCS)),
		);
		$this->roles['roles'][PERMISSIONS_PLS] = array(
			'label' => 'PL',
			'value' => $roles[PERMISSIONS_PLS],
			'checked' => (in_array($roles[PERMISSIONS_PLS], $this->data['user_groups'])),
			'disabled' => !$this->librivox_auth->in_group(array(PERMISSIONS_ADMIN, PERMISSIONS_MCS)),
		);
		$this->roles['roles'][PERMISSIONS_BCS] = array(
			'label' => 'BC',
			'value' => $roles[PERMISSIONS_BCS],
			'checked' => (in_array($roles[PERMISSIONS_BCS], $this->data['user_groups'])),
			'disabled' => !$this->librivox_auth->in_group(array(PERMISSIONS_ADMIN, PERMISSIONS_MCS)),
		);
		$this->roles['roles'][PERMISSIONS_MCS] = array(
			'label' => 'MC',
			'value' => $roles[PERMISSIONS_MCS],
			'checked' => (in_array($roles[PERMISSIONS_MCS], $this->data['user_groups'])),
			'disabled' => !$this->librivox_auth->is_admin(),
		);

		$this->data['profile_modal'] = $this->load->view('private/common/profile_modal', $this->roles, true);
	}

	public function _render($view, $data)
	{
		$this->template->write_view('head', 'common/workflow_head.php', $data);
		$this->template->write_view('content_left', $view, $data);
		$this->template->render();
	}
}
