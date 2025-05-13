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
		
		$this->load->helper('file');
		write_file(CACHE_DIR . KEYWORDS_CACHE_FILENAME, $json_encoded_keywords_cache);
	}
}
