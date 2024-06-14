<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_write_keywords_initial_cache extends CI_Migration {

        public function up()
        {

		$json_encoded_keywords_cache = '';
		
		$sql = 'SELECT DISTINCT k.value
		FROM keywords k
		JOIN project_keywords pk
		ON k.id = pk.keyword_id
		ORDER BY value ASC';
		$query = $this->db->query($sql);
		$json_encoded_keywords_cache = json_encode($query->result_array());
		
		// Use following two lines to create a file-based cache.
		$this->load->helper('file');
		write_file(CACHE_DIR . KEYWORDS_CACHE_FILENAME, $json_encoded_keywords_cache);
		
		// Use following line to create an apcu cache.

		// apcu_add(KEYWORDS_CACHE_KEY_APCU, $json_encoded_keywords_cache);

        }

        public function down()
        {
                ;
        }
}
