<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group_model extends MY_Model {

	function get_group_details($group_id)
	{

		$query = $this->db
		->select('p.id, TRIM(CONCAT(p.title_prefix, " ", p.title)) as title', false)
		->join('projects p', 'gp.project_id = p.id')
		->where('gp.group_id', $group_id)
		->get('group_projects gp');

		return $query->result_array();
	}

	function get_group_by_project($project_id =0)
	{
		$query = $this->db
		->select('g.id AS group_id, g.name AS group_name', false)
		->join('group_projects gp', 'gp.group_id = g.id')
		->where('gp.project_id', $project_id)
		->get('groups g');

		if ($query->num_rows() != 1) return false;

		return $query->row();		
	}

}

/* End of file group_model.php */
/* Location: ./application/models/group_model.php */