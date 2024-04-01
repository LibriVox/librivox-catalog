<?php

class Migrate extends CI_Controller
{

    public function migrate()
    {
        $this->load->library('migration');
        $target_version = $this->migration->current();
        if ($target_version)
        {
            $this->migration->version($target_version);
        } else {
            show_error($this->migration->error_string());
        } 
    }
}
