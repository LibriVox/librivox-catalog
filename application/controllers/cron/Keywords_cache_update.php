<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Keywords_cache_update extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	//update keywords cache
	public function update_cache()
	{
		$json_encoded_keywords_cache = '';
		
		$sql = 'SELECT DISTINCT k.value
		FROM keywords k
		JOIN project_keywords pk
		ON k.id = pk.keyword_id
		ORDER BY value ASC';
		$query = $this->db->query($sql);
		$json_encoded_keywords_cache = json_encode($query->result_array());
		
		// Use following two lines for file-based cache.
		// If using file-based cache, make sure that autocomplete() function in 
		// application/models/Keyword_model.php is also using file-based caching

		$this->load->helper('file');
		write_file(CACHE_DIR . KEYWORDS_CACHE_FILENAME, $json_encoded_keywords_cache);

/**		
		// Use following for apcu cache.
		// If using apcu cache, make sure that autocomplete() function in 
		// application/models/Keyword_model.php is also using apcu caching
	
		if (apcu_exists(KEYWORDS_CACHE_KEY_APCU))
		{			
			// overwrite existing data for this key
			apcu_store(KEYWORDS_CACHE_KEY_APCU, $json_encoded_keywords_cache);
		}
		else
		{
			// must use apcu_add if key not already present in cache
			apcu_add(KEYWORDS_CACHE_KEY_APCU, $json_encoded_keywords_cache);
		}
**/

	}
}
