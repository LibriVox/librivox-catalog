<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_keywords_instances_field extends CI_Migration {

        public function up()
        {
		$fields = array(
        		'instances' => array('type' => 'INT',
			'default' => '1',
			'after' => 'value')
		);
		$this->dbforge->add_column('keywords', $fields);
        }

        public function down()
        {
                $this->dbforge->drop_column('keywords', 'instances');
        }
}

