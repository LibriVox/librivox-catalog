<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Keyword_model extends MY_Model {

    public function get_applied($list_keywords)
    {
    	$keyword_array = explode(',', $list_keywords);
    	return $this->db->where_in($this->primary_key, $keyword_array)->get($this->_table)->result();


    }

	//return comma delimited list of keywords from project
	public function create_keyword_list($project_id)
	{
		$sql = 'SELECT k.value
		FROM keywords k
		JOIN project_keywords pk ON (pk.keyword_id = k.id)
		WHERE pk.project_id = ?';

		$query = $this->db->query($sql, array($project_id));

		$keyword_list = array();

		if ($query->num_rows())
		{
			foreach ($query->result() as $keyword)
			{
				$keyword_list[] = $keyword->value;
			}	
		}
		//archive needs a ;
		return implode('; ', $keyword_list);	
	}	    

}

/* End of file keyword_model.php */
/* Location: ./application/models/keyword_model.php */
