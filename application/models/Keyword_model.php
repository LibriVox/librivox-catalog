<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Keyword_model extends MY_Model {

    public function get_applied($list_keywords)
    {
    	$keyword_array = explode(',', $list_keywords);
    	return $this->db->where_in($this->primary_key, $keyword_array)->get($this->_table)->result();


    }
    
 /**   
    	public function get_all_keywords_used_in_projects()
    	{
    		error_log("In get_all_ keywords_used_in_project");
    		$sql = 'SELECT DISTINCT k.value
		FROM keywords k
		JOIN project_keywords pk
		ON k.id = pk.keyword_id';
		
		$query = $this->db->query($sql);
		return $query->result_array();
    	}
    	
  **/  	
    	
    	public function autocomplete($term)
	{
		// To minimise the number of database calls in our system if multiple
		// users are entering values in the Advanced Search Keywords field at the
		// same time, we cache a list of keywords used in projects on the server file system,
		// and interrogate that to populate the Keywords field dropdown
		
	
		$json_encoded_keywords_cache_as_read_from_file = '';
		$cached_result_array = array();
		
		// Use following two lines for file-based cache.
		// If using file-based cache, make sure that /application/controllers/cron/Keywords_cache_update
		// is writing a file-based cache
		$this->load->helper('file');
		$json_encoded_keywords_cache = read_file(CACHE_DIR . KEYWORDS_CACHE_FILENAME);
		
		// Use following line for apcu cache.
		// If using apcu cache, make sure that /application/controllers/cron/Keywords_cache_update
		// is writing to apcu cache
		
		// $json_encoded_keywords_cache = apcu_fetch(KEYWORDS_CACHE_KEY_APCU);
		
		$associative = true;
		$keywords_cache_array = json_decode($json_encoded_keywords_cache, $associative);
			
		foreach($keywords_cache_array as $row => $inner_array)
		{
  			foreach($inner_array as $inner_row => $value)
  			{
    				// Does the term match the start of a keyword in use?
    				// Our match is case-insensitive.
    				if ( stripos($value, $term)  === 0 )
				{						
					$new_element = ["value" => $value];
					array_push($cached_result_array, $new_element);

				}
  			}
  			$array_size = count ($cached_result_array);
			if ( count ($cached_result_array) >= AUTOCOMPLETE_LIMIT) 
			{
				break;
			}
		}
			
		// If we have at least one good match above, and if we have not yet exceeded 
		// the number of items we can show in our dropdown list, show some additional partial matches
			
		if (( count ($cached_result_array) <= AUTOCOMPLETE_LIMIT) and (count ($cached_result_array) > 0))
		{
			$divider = "=== some other keywords containing '" . $term . "' ===";
			$new_element = ["value" => $divider];
			array_push($cached_result_array, $new_element);
			
			foreach($keywords_cache_array as $row => $inner_array)
			{
  				foreach($inner_array as $inner_row => $value)
  				{
    					// Does the term occur in a keyword in use, but not at its start?
    					// Our match is case-insensitive.
    					if ( (stripos($value, $term)  !== 0 ) and ((stripos($value, $term))) )
					{						
						$new_element = ["value" => $value];
						array_push($cached_result_array, $new_element);
					}
  				}
  				$array_size = count ($cached_result_array);
				if ( count ($cached_result_array) >= AUTOCOMPLETE_LIMIT) 
				{
					break;
				}
			}
		}
					
		return $cached_result_array;
	}  // end of function
		
/**
		// The following code achieves a result similar to the cache-based system
		// above, but without case-insensitive matching. The argument for favouring
		// a cache-based system over the one below is that the one below makes heavier
		// demands on our database and shows poorer performance under load testing.
		
		// Leaving the code below here for the present to facilitate easy comparison
		// of caching versus non-caching approaches
		
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
**/

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
