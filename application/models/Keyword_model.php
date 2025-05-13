<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Keyword_model extends MY_Model {

    public function get_applied($list_keywords)
    {
    	$keyword_array = explode(',', $list_keywords);
    	return $this->db->where_in($this->primary_key, $keyword_array)->get($this->_table)->result();


    }
    
    public function autocomplete($term)
	{
		// To minimise the number of database calls in our system if multiple
		// users are entering values in the Advanced Search Keywords field at the
		// same time, we cache a list of keywords used in projects on the server file system,
		// and interrogate that to populate the Keywords field dropdown
		
	
		$json_encoded_keywords_cache_as_read_from_file = '';
		$cached_result_array = array();
		
		$this->load->helper('file');
		$json_encoded_keywords_cache = read_file(CACHE_DIR . KEYWORDS_CACHE_FILENAME);
		
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
