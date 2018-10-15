<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project_reader_model extends MY_Model {

	function get_all_distinct($offset=0, $limit=CATALOG_RESULT_COUNT){

		$sql = "SELECT DISTINCT reader_id, display_name, username, COALESCE(NULLIF(display_name,''), username)
				FROM project_readers
				ORDER BY 4 ASC ";

		if ($limit) $sql .= " LIMIT $offset, $limit";

		$query = $this->db->query($sql); 		
		return $query->result_array();	

	}

}	