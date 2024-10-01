<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_keywords_count_field extends CI_Migration {

        public function up()
        {
		$fields = array(
        		'count' => array('type' => 'INT',
			'default' => '0',
			'after' => 'value')
		);
		$this->dbforge->add_column('keywords', $fields);
        }

        public function down()
        {
                $this->dbforge->drop_column('keywords', 'count');
        }
}

