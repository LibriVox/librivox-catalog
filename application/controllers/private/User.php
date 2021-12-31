<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends Private_Controller
{

	public function get_profile()
	{
		$this->load->helper('array');
		$this->load->model('user_model');

		$fields = $this->input->post(null, true);
		$user_id = (empty($fields['user_id'])) ? $this->data['user_id'] : $fields['user_id'];

		$is_admin = $this->librivox_auth->has_permission(array(PERMISSIONS_ADMIN), $this->data['user_id']);
		$is_mc = $this->librivox_auth->has_permission(array(PERMISSIONS_ADMIN, PERMISSIONS_MCS), $this->data['user_id']);

		// check permissions
		if (!$is_mc && ($this->data['user_id'] != $user_id))
			$this->ajax_output(array('message' => 'No access to this user record'), false);

		// get user record using field whitelist so we do not send un-needed items or things
		// like the password hash which are a security risk
		$user = elements(
			array('id', 'username', 'email', 'max_projects', 'display_name', 'website'),
			(array)$this->user_model->get($user_id)
		);

		$user['user_groups'] = $this->librivox_auth->get_users_groups($user_id)->result_array();

		// you can only change your own password here, not via
		$user['show_password'] = ($this->data['user_id'] == $user_id);

		// permissions used by form to hide/disable certain fields
		$user['is_admin'] = $is_admin;
		$user['is_mc'] = $is_mc;

		$this->ajax_output(array('user' => $user), true);
	}

	public function update_profile()
	{
		$this->load->helper('array');

		$fields = $this->input->post(null, true);
		$user_id = (empty($fields['user_id'])) ? $this->data['user_id'] : $fields['user_id'];

		$is_admin = $this->librivox_auth->has_permission(array(PERMISSIONS_ADMIN), $this->data['user_id']);
		$is_mc = $this->librivox_auth->has_permission(array(PERMISSIONS_ADMIN, PERMISSIONS_MCS), $this->data['user_id']);

		if (!$is_mc && ($this->data['user_id'] != $user_id))
			$this->ajax_output(array('message' => 'No permissions for this action'), false);

		if (!isset($fields['groups']) || !is_array($fields['groups']))
			$this->ajax_output(array('message' => 'groups array is required'), false);

		// fields anyone can update
		$allowed_fields = array('email');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

		// fields only MCs (or admins) can update
		if ($is_mc)
		{
			array_push($allowed_fields, 'display_name', 'website', 'max_projects');
			$this->form_validation->set_rules('display_name', 'Display Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('website', 'Website', 'trim|xss_clean');
			$this->form_validation->set_rules('max_projects', 'Max Project Count', 'trim|required|xss_clean|is_natural');
		}

		// fields only admins can update
		if ($is_admin)
		{
			$allowed_fields[] = 'username';
			$this->form_validation->set_rules('username', 'User Name', 'trim|required|xss_clean|alpha_dash');
		}

		// fields which the user can only update for themselves
		if ($this->data['user_id'] == $user_id)
		{
			if (!empty($fields['password']))
			{
				$allowed_fields[] = 'password';
				$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[8]');
				$this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|xss_clean|required|matches[password]|min_length[8]');
			}
		}

		if (!$this->form_validation->run())
			$this->ajax_output(array('message' => validation_errors('<div class="alert alert-error">', '</div>')), false);

		// need to get fields again as validation may have changed them
		$fields = $this->input->post(null, true);

		$update = elements($allowed_fields, $fields);
		$this->ion_auth->update($user_id, $update);
		$this->_update_groups($fields['groups'], $user_id);

		$this->ajax_output(array('message' => 'Updated'), true);
	}

	public function add_profile()
	{
		//check permissions
		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS, PERMISSIONS_BCS);
		if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			$this->ajax_output(array('message' => 'No permissions for this action'), false);
		}

		$fields = $this->input->post(null, true);

		//validate - username, displayname required
		if (empty($fields['username']) || empty($fields['display_name']))
			$this->ajax_output(array('message' => 'Username and Display name are required'), false);

		if (!isset($fields['groups']) || !is_array($fields['groups']))
			$this->ajax_output(array('message' => 'groups array is required'), false);

		$this->load->helper('string');

		$username = $fields['username'];
		$password = random_string('alnum', 8);

		// we will now try to get this from forum
		$this->load->model('forum_user_model');
		$forum_user = $this->forum_user_model->get_by(array('username' => $username));

		//reset db connection
		$this->db = $this->load->database('default', TRUE);

		if (!$forum_user) $this->ajax_output(array('message' => 'Forum user email not found'), false);
		$email = $forum_user->user_email;

		$additional_data['display_name'] = $fields['display_name'];
		if (!empty($fields['website']))
			$additional_data['website'] = $fields['website'];

		$groups = $this->_update_groups($fields['groups']);

		$this->librivox_auth->register($username, $password, $email, $additional_data, $groups);

		$this->ajax_output(array('message' => 'User Added'), true);
	}

	// This function filters the passed in groups based on permissions of the current
	// user and returns the filtered list. If user_id is set this function will also
	// update the groups in the database but will leave groups you do not have permissions
	// to change untouched (even if they are missing from the passed in groups).
	function _update_groups($groups, $user_id = 0)
	{
		//the docs lie - only group ids are accepted, not names (unless the code library has been updated?)
		$roles = $this->config->item('roles');
		$allowed_groups = array();

		if ($this->librivox_auth->in_group(array(PERMISSIONS_ADMIN, PERMISSIONS_MCS, PERMISSIONS_BCS)))
		{
			$allowed_groups[] = $roles[PERMISSIONS_MEMBERS];
			$allowed_groups[] = $roles[PERMISSIONS_READERS];
		}

		if ($this->librivox_auth->in_group(array(PERMISSIONS_ADMIN, PERMISSIONS_MCS)))
		{
			$allowed_groups[] = $roles[PERMISSIONS_PLS];
			$allowed_groups[] = $roles[PERMISSIONS_BCS];
		}

		if ($this->librivox_auth->is_admin())
		{
			$allowed_groups[] = $roles[PERMISSIONS_MCS];
		}

		//always add this
		array_push($groups, $roles[PERMISSIONS_MEMBERS]);

		//we only want the intersection of valid roles & requested groups
		$groups = array_intersect($allowed_groups, $groups);

		// update groups in database if user_id was set
		if ($user_id)
		{
			foreach ($allowed_groups as $key => $group_id)
			{
				$requested = in_array($group_id, $groups);
				if ($this->librivox_auth->in_group($group_id, $user_id))
				{
					if (!$requested)
						$this->librivox_auth->remove_from_group($group_id, $user_id);
				}
				else
				{
					if ($requested)
						$this->librivox_auth->add_to_group($group_id, $user_id);
				}
			}
		}

		return $groups;
	}

	///////  TESTING /////

	function test_update_groups()
	{
		$groups = array(3, 1, 4, 5);
		$array = $this->_update_groups($groups);
		var_dump($array);
	}

	function test_update_profile()
	{
		$_POST['user_id'] = 20;
		$_POST['display_name'] = 'Gesine';
		$_POST['email'] = 'justgesine@yahoo.co.uk';
		$_POST['max_projects'] = 3;

		$_POST['groups[]'] = 3;
		$_POST['groups[]'] = 5;
		$_POST['groups[]'] = 0;

		$this->form_validation->set_data($_POST);

		$this->update_profile();

		echo 'Success';
	}

	function test_add_profile()
	{
		//$_POST['user_id'] 	= 20;
		$_POST['username'] = 'KayTee2407';
		$_POST['display_name'] = 'KayTee2407';
		//$_POST['email'] 			= 'justgesine@yahoo.co.uk';
		//$_POST['max_projects'] 	= 3;

		$_POST['groups[]'] = 3;
		//$_POST['groups[]'] 	= 7;
		//$_POST['groups[]'] 	= 4;

		$_POST['website'] = '';

		$this->form_validation->set_data($_POST);

		$this->add_profile();

		echo 'Success';
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
