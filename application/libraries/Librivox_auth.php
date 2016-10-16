<?php

class Librivox_auth extends Ion_Auth{

	function get_user_id()
	{

		$user_id = $this->session->userdata('user_id');
		if (!empty($user_id))
		{
			return $user_id;
		}	
		return 0;
	}

	//this is just a wrapper for in_group() so we can add extra criteria such as $project_id, etc
	function has_permission($allowed_groups, $user_id)
	{
		if (empty($allowed_groups) || empty($user_id)) return false;

		foreach ($allowed_groups as $key => $check_group) {
			if ($this->in_group($check_group, $user_id)) return true;
		}

		return false;		
	}



}