<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_zip_urls_2 extends CI_Migration {

        public function up()
        {
                $this->db->query('
                        UPDATE projects
                        SET zip_url = REGEXP_REPLACE(zip_url, ".*archive.org/download/(.*)/(.*)_[0-9]+kb_mp3.zip", "https://archive.org/compress/\\\1/formats=64KBPS MP3&file=/\\\2.zip")
                        WHERE zip_url LIKE "%archive.org/download/%"
                ');
        }

        public function down(): void
        {}
}