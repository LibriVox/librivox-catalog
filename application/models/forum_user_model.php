<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forum_user_model extends MY_Model {

    public function __construct()
    {
       //$this->librivox_forum  = $this->load->database('librivox_forum', TRUE);	
       

       $this->_db = 'librivox_forum';

       $this->_table = 'catalog_users'; //view

       $this->primary_key = 'username';

       parent::__construct();
    }

}    