<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Public_readers_iframe extends CI_Controller {

   public function index($project_id)
   {
   		if(empty($project_id)) return false; //some sort of error handling

   		$this->load->model('project_model');
   		$this->data['project'] 	= $this->project_model->get($project_id);

   		if(empty($this->data['project'])) return false; //some sort of error handling

   		$this->load->helper('general_functions_helper');


   		
   		$this->data['pl'] = '';

   		if ($this->data['project']->person_pl_id)
   		{
   			$this->load->model('user_model');
   			$pl = $this->user_model->get($this->data['project']->person_pl_id);
   			if(!empty($pl))
   			{
   				$this->data['pl_username'] = $pl->username;
   			}
   		}

   		//need sections, readers, section status

   		$this->load->model('section_model');
   		$this->data['section_info'] = $this->section_model->get_full_sections_info($project_id);

   		$this->data['section_assigned'] = 0;
   		$this->data['section_complete'] = 0;
   		
   		// a little tidying up...
   		foreach($this->data['section_info'] as $key=>$section)
   		{

            $this->data['section_info'][$key]->reader = '';
   			
   			if (!empty($section->readers[0])) 
            {
               $this->data['section_assigned']++;               
               $this->data['section_info'][$key]->reader = $section->readers[0]->reader_name;
               $this->data['section_info'][$key]->reader_id = $section->readers[0]->reader_id;
            }   

   			if(strtolower($section->status) == 'pl ok') $this->data['section_complete']++;

   		}	

   		$this->data['section_count'] = count($this->data['section_info'] ); 


   		$this->load->view('public/public_readers_iframe/index',$this->data);

   }

}