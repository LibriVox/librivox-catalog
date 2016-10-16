<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	function get_user_id()
	{
		$ci = &get_instance();

		$user_id = $ci->session->userdata('user_id');
		if (!empty($user_id))
		{
			return $user_id;
		}	
		return 0;
	}

	/*
	function get_group_name()
	{
		$ci = &get_instance();

		$group_name = $ci->session->userdata('group_name');
		if (!empty($group_name))
		{
			return $group_name;
		}	
		return 0;
	}
	*/	