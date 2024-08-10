<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_genre_sort extends CI_Migration {

        public function up()
        {
                // Fix 'lineage' being required.  It is set immediately _after_ a genre is created.
                $fields = array(
                        'lineage' => array(
                                'type' => 'text',
                                'default' => ''
                        )
                );
                $this->dbforge->modify_column('genres', $fields);

                // Add 'sort_order'.  This is relative to other items within the same sub-menu.
                $fields = array(
                        'sort_order' => array(
                                'type' => 'INT',
                                'default' => '20',
                                'after' => 'meta_in_progress'
                        )
                );
                $this->dbforge->add_column('genres', $fields);

                // Set the desired sort order for existing genre items
                $explicit_orders = array(
                        array('id' => 52, 'sort_order' => 15), // General fiction -> Published before 1800

                        array('id' => 113, 'sort_order' => 10), // Non-Fiction -> History -> Antiquity
                        array('id' => 114, 'sort_order' => 15), // Non-Fiction -> History -> Middle Ages/Middle History

                        array('id' => 123, 'sort_order' => 10), // Non-Fiction -> Philosophy -> Ancient
                        array('id' => 124, 'sort_order' => 15), // Non-Fiction -> Philosophy -> Medieval
                        array('id' => 127, 'sort_order' => 25), // Non-Fiction -> Philosophy -> Contemporary
                        array('id' => 143, 'sort_order' => 30), // Non-Fiction -> Philosophy -> Atheism & Agnosticism
                );
                $this->db->update_batch('genres', $explicit_orders, 'id');
        }

        public function down()
        {
                $this->dbforge->drop_column('genres', 'sort_order');
        }
}

