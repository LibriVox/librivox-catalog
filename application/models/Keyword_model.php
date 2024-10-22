<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Keyword_model extends MY_Model {

    public function get_applied($list_keywords)
    {
    	$keyword_array = explode(',', $list_keywords);
    	return $this->db->where_in($this->primary_key, $keyword_array)->get($this->_table)->result();


    }
    
    	public function autocomplete($term)
	{
		// Escaping -- https://www.codeigniter.com/userguide3/database/queries.html#escaping-queries
		$escaped_term = $this->db->escape_like_str($term);
		
		// For extra safety, parameterise the query as well
		
		$params = [];
		array_push($params, $escaped_term . "%");
		array_push($params, $escaped_term);
		array_push($params, "%" . $escaped_term . "%");
		array_push($params, $escaped_term . "%");

		$sql = 'SELECT DISTINCT k.value, "A" AS priority
		FROM keywords k
		JOIN project_keywords pk
		ON k.id = pk.keyword_id
		WHERE k.value LIKE ?  
		UNION 
		SELECT "=== some other keywords containing \"?\" ===" AS value, "B" AS priority
		UNION
		SELECT DISTINCT k.value, "C" AS priority
		FROM keywords k
		JOIN project_keywords pk
		ON k.id = pk.keyword_id
		WHERE k.value LIKE ?  
			AND k.value NOT LIKE ?  		
		ORDER BY priority ASC, value ASC
		LIMIT 200';
		
		$query = $this->db->query($sql, $params);
		return $query->result_array();
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
