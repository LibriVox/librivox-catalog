<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Form_generators_authors_model extends MY_Model {

    public function __construct()
    {
       parent::__construct();
       
       $this->primary_key = 'auth_id';
    }

	//uses comma-delimited list of author ids in a where_in
	public function get_author_list($auth_id_list)
	{
		$sql = '
		SELECT auth_id AS id,auth_first_name AS first_name, auth_last_name AS last_name, link_to_auth AS author_url, auth_yob AS dob, auth_yod AS dod 
		FROM '. $this->_table . '
		WHERE '. $this->primary_key .' IN (' .$auth_id_list. ')';
		$query = $this->db->query($sql);

		return $query->result_array();
	}    

}

/* End of file form_generators_authors_model.php */
/* Location: ./application/models/form_generators_authors_model.php */
