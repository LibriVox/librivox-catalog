<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends Private_Controller
{

	public function get_profile()
	{
		$this->load->helper('array');
		$this->load->model('user_model');

		$fields = $this->input->post(null, true);
		$user_id = (empty($fields['user_id'])) ? $this->data['user_id'] : $fields['user_id'];

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

		// you can only change your own password here, not via
		$user['show_password'] = false;
		if (($this->data['user_id'] == $user_id))
		{
			$user['show_password'] = true;
		}

		$user['show_groups'] = false;
		if ($is_mc)
		{
			$user['user_groups'] = $this->librivox_auth->get_users_groups($user_id)->result_array();
			$user['show_groups'] = true;
		}

		$retval = array('user' => $user);
		$this->ajax_output($retval, true);
	}

	public function update_profile()
	{
		$fields = $this->input->post(null, true);
		$user_id = (empty($fields['user_id'])) ? $this->data['user_id'] : $fields['user_id'];

		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
		if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']) && ($this->data['user_id'] != $user_id))
		{
			$this->ajax_output(array('message' => 'No permissions for this action'), false);
		}

		//we want to reuse this for the profile, but also just changing active status
		if (!isset($fields['active']))
		{
			$this->form_validation->set_rules('display_name', 'Display Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
			$this->form_validation->set_rules('max_projects', 'Max Project Count', 'trim|required|xss_clean|is_natural');

			if (!empty($fields['password']))
			{
				$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[8]');
				$this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|xss_clean|required|matches[password]|min_length[8]');
			}

			if (!$this->form_validation->run()) $this->ajax_output(array('message' => validation_errors('<div class="alert alert-error">', '</div>')), false);
		}

		//allowable updates
		$this->load->helper('array');
		$update = elements(array('display_name', 'email', 'website', 'max_projects', 'password'), $fields, NULL);

		//admins can update usernames
		if ($this->librivox_auth->has_permission(array(PERMISSIONS_ADMIN), $this->data['user_id']))
		{
			$update['username'] = $fields['username'];
		}

		//let's simplify - only Admins & MCs can change groups
		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
		$group_ids = array();
		if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			//vet groups post to only add users to roles logged in user has permissions for

			if (!empty($fields['groups']))
			{
				$group_ids = $this->_vet_roles($fields['groups'], $user_id);

				if (!empty($group_ids))
				{
					//remove from groups, then rebuild
					$this->librivox_auth->remove_from_group(false, $user_id);

					foreach ($group_ids as $key => $group_id)
					{
						$this->librivox_auth->add_to_group($group_id, $user_id);
					}
				}
			}
		}

		$this->ion_auth->update($user_id, $update);

		$this->ajax_output(array('message' => 'Updated', 'group_ids' => $group_ids), true);
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
		if (empty($fields['username']) || empty($fields['display_name'])) $this->ajax_output(array('message' => 'Username and Display name are required'), false);

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
		$additional_data['website'] = $fields['website'];

		$group_name = array();

		//vet groups post to only add users to roles logged in user has permissions for
		if (!empty($fields['groups']))
		{
			$group_name = $this->_vet_roles($fields['groups'], 0);
		}

		$this->librivox_auth->register($username, $password, $email, $additional_data, $group_name);

		$retval = array('message' => 'User Added');
		$this->ajax_output($retval, true);
	}

	function _vet_roles($groups, $user_id = 0)
	{
		//the docs lie - only group ids are accepted, not names (unless the code library has been updated?)
		$roles = $this->config->item('roles');

		$valid_array = array($roles[PERMISSIONS_READERS], $roles[PERMISSIONS_PLS], $roles[PERMISSIONS_MEMBERS]);

		//only an admin can add an MC; NO ONE can add an admin here!
		if ($this->librivox_auth->is_admin())
		{
			array_push($valid_array, $roles[PERMISSIONS_MCS]);
		}

		if ($this->librivox_auth->in_group(array(PERMISSIONS_ADMIN, PERMISSIONS_MCS), $this->librivox_auth->get_user_id()))
		{
			array_push($valid_array, $roles[PERMISSIONS_BCS]);
		}

		//always add this
		array_push($groups, $roles[PERMISSIONS_MEMBERS]);

		//we only want the intersection of valid roles & requested groups
		$valid_array = array_intersect($valid_array, $groups);

		//if this is your own account, re-add admin && mc roles
		if ($this->librivox_auth->get_user_id() == $user_id)
		{
			if ($this->librivox_auth->in_group(array(PERMISSIONS_ADMIN), $this->librivox_auth->get_user_id()))
			{
				array_push($valid_array, $roles[PERMISSIONS_ADMIN]);
			}
			if ($this->librivox_auth->in_group(array(PERMISSIONS_MCS), $this->librivox_auth->get_user_id()))
			{
				array_push($valid_array, $roles[PERMISSIONS_MCS]);
			}
		}

		return $valid_array;
	}

	///////  TESTING /////

	function test_vet_roles()
	{
		$groups = array(3, 1, 4, 5);
		$array = $this->_vet_roles($groups, 7482);
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
