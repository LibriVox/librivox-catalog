<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Drop_redundant_keywords_from_projects extends CI_Migration {

        public function up()
        {
    		$sql = '
		DELETE pk2.*
		FROM project_keywords pk2
		WHERE keyword_id IN
		(
			SELECT id 
			FROM keywords k 
			WHERE k.value in (
			"librivox", 
			"ibrivox",
			"llibrivox",
			"librixox",
			"librivox;",
			"libriox",
			"libirivox",
			"librivos",
			"audio",
			"audiobook",
			"audiobooks",
			"audio books",
			"audio book",
			"audioboek",
			"audiobuch",
			"audiobooklibrivox",
			"audibook",
			"audioook",
			"autiobook",
			"audiobook:",
			"audioboo",
			"audiolibro",
			"audio libro",
			"audiolibros",
			"audiolivre",
			"audiolivro"
			)
		)';
    		$this->db->query($sql);
        }

        public function down()
        {
                ;
        }
}
