<?php

class DatabaseSeeder extends Seeder {

	public function run()
	{
        $filename = __DIR__ . '/librivox_catalog_scrubbed.sql.bz2';
        $dump = fopen("compress.bzip2://$filename", 'r');
        $query = '';
        while (True)
        {
            $line = fgets($dump);
            if (! $line) break;
            if (str_starts_with($line, '--')) continue;
            $line = trim($line, "\r\n");
            $query = $query . $line;
            if (str_ends_with($query, ';'))
            {
                $this->db->query($query);
                $query = '';
            }
        }
        fclose($dump);
	}
}
