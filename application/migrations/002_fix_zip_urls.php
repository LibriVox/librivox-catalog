<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_zip_urls extends CI_Migration {

        public function up()
        {
                $this->db->query('
                        UPDATE projects
                        SET zip_url = REGEXP_REPLACE(zip_url, "download//", "download/")
                        WHERE zip_url LIKE "https://www.archive.org/download//%"
                ');
        }

        public function down(): void
        {}
}