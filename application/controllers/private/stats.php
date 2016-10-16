<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stats extends Private_Controller {

   public function __construct()
   {
      parent::__construct();


      $this->template->add_css('css/libs/datatable.css');
      $this->template->add_js('js/libs/jquery.dataTables.js');
      $this->template->add_js('js/libs/jquery.jeditable.js');

      $this->load->model('stats_model');
   }


	public function index($user_id=0)
	{
   
    
      $user_id = ($user_id)? $user_id: $this->librivox_auth->get_user_id();

		  $this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

      $this->load->model('project_model');

      // This will not fully run - queries need re-write
      //$this->data['active_stats'] = $this->project_model->get_projects_summary($user_id);      
      //$this->data['inactive_stats'] = $this->project_model->get_projects_summary($user_id, 'inactive');

		  $this->insertMethodCSS();
    	$this->insertMethodJS();

     	$this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);			
  		$this->template->render();   
      
	}



  public function mc_stats()
  {

      //check permissions
      $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
      if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
      {
         redirect(base_url().'/auth/error_no_permission');
      }

    
      //https://catalog.librivox.org/MCstats.php
      $this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

      $this->data['volunteers'] = $this->stats_model->mc_stats();

      $this->insertMethodCSS();
      $this->insertMethodJS();

      $this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);      
      $this->template->render();     
  }

  public function monthly_stats()
  {
      //https://catalog.librivox.org/monthly.php
      $this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

      $this->data['monthly_stats'] = $this->stats_model->monthly_stats();
      $this->data['yearly_stats'] = $this->stats_model->yearly_stats();


      $this->insertMethodCSS();
      $this->insertMethodJS();

      $this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);      
      $this->template->render();     
  }  



  public function general_stats()
  {
      //https://catalog.librivox.org/stats.php -- general stats page
      $this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);


      //$this->data['stats'] = $this->stats_model->monthly_stats();
      $this->data['project_count'] = $this->stats_model->project_count();
      $this->data['project_count_completed'] = $this->stats_model->project_count_completed();
      $this->data['project_count_completed_nonenglish'] = $this->stats_model->project_count_completed_nonenglish();
      $this->data['language_count'] = $this->stats_model->language_count();
      $this->data['language_count_with_completed'] = $this->stats_model->language_count_with_completed();
      $this->data['project_count_completed_solo'] = $this->stats_model->project_count_completed_solo();
      $this->data['reader_count'] = $this->stats_model->reader_count();
      $this->data['reader_count_with_completed'] = $this->stats_model->reader_count_with_completed();


      $this->insertMethodCSS();
      $this->insertMethodJS();

      $this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);      
      $this->template->render();     
  }


  public function chapters_count($user_id=0)
  {
     $user_id = ($user_id)? $user_id: $this->librivox_auth->get_user_id();

      //https://catalog.librivox.org/chapters_count.php
      $this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);  



      $this->insertMethodCSS();
      $this->insertMethodJS();

      $this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);      
      $this->template->render();  

  }



  public function sections($user_id = 0)
  {

      $user_id = ($user_id)? $user_id: $this->librivox_auth->get_user_id();


      //https://catalog.librivox.org/chapters_count.php
      $this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

      $this->load->model('section_model');

      $projects = $this->section_model->get_sections_by_reader($user_id, 'reader');


      foreach($projects as $key=>$project)
      {
        // "Assigned", "Ready for PL", "See PL notes", "Ready for spot PL", "PL OK"

          if (!isset($this->data['reader_stats'][$project->id]))
          {
            $this->data['reader_stats'][$project->id] = array('title_prefix'=>$project->title_prefix, 'title'=>$project->title, 'status'=>$project->status, 'url_forum'=>$project->url_forum);            
          }  

          $key = strtolower(str_replace(' ', '_', $project->section_status));

          if (isset($this->data['reader_stats'][$project->id][$key]))
          {
            $this->data['reader_stats'][$project->id][$key] .= ', ' . $project->section_number; 
          } 
          else
          {
            $this->data['reader_stats'][$project->id][$key] = $project->section_number;
          } 
       
      }  


      $projects = $this->section_model->get_sections_by_reader($user_id, 'pl');

      // echo $this->db->last_query();

      foreach($projects as $key=>$project)
      {
        // "Assigned", "Ready for PL", "See PL notes", "Ready for spot PL", "PL OK"

          if (!isset($this->data['pl_stats'][$project->id]))
          {
            $this->data['pl_stats'][$project->id] = array('title_prefix'=>$project->title_prefix, 'title'=>$project->title, 'status'=>$project->status, 'url_forum'=>$project->url_forum);
          }

          $key = strtolower(str_replace(' ', '_', $project->section_status));

          if (isset($this->data['pl_stats'][$project->id][$key]))
          {
            $this->data['pl_stats'][$project->id][$key] .= ', ' . $project->section_number; 
          } 
          else
          {
            $this->data['pl_stats'][$project->id][$key] = $project->section_number;
          }     

      } 

      $this->config->load('librivox');
      $this->data['project_statuses'] = $this->config->item('project_statuses');

      $this->insertMethodCSS();
      $this->insertMethodJS();

      $this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);      
      $this->template->render();   

  }






  //updates stats
  public function statistics()
  {
      $array['total_projects']       = $this->stats_model->project_count_completed();
      $array['projects_last_month']  = $this->stats_model->monthly_stats(1, 1)->project_count;
      $array['non_english_projects'] = $this->stats_model->project_count_completed_nonenglish();
      $array['number_languages']     = $this->stats_model->language_count_with_completed();
      $array['number_readers']       = $this->stats_model->reader_count();
      $array['updated']              = time();

      $this->load->model('statistics_model');
      $this->statistics_model->insert($array);
  }


  public function active_projects()
  {
      //check permissions
      $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
      if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
      {
         redirect(base_url().'/auth/error_no_permission');
      }

      $this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

      $this->data['projects'] = $this->stats_model->active_projects();

      $this->load->model('user_model');
      $this->data['project_mcs']  = $this->user_model->get_dropdown_by_role('mc', false);
      $this->data['project_pls']  = $this->user_model->get_dropdown_by_role('pl', false);

      //var_dump($pls);

      $this->config->load('librivox');
      $this->data['project_statuses'] = $this->config->item('project_statuses');

      $this->load->model('section_model');

      $this->section_model->create_temp_section_status_count();
      $this->section_model->truncate_temp_section_status_count();
      $this->section_model->populate_section_status_count();


      foreach ($this->data['projects'] as $key => $project) {
          $project->complete = $this->section_model->get_count_by_status($project->id, array("PL OK"));
          $project->assigned = $this->section_model->get_count_by_status($project->id, array("Assigned", "Ready for PL", "See PL notes", "Ready for spot PL", "PL OK"));

          $project->assigned_pct = number_format($project->assigned / $project->num_sections, 2) * 100;
          $project->complete_pct = number_format($project->complete / $project->num_sections, 2) * 100;
      }


      $this->insertMethodCSS();
      $this->insertMethodJS();

      $this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);      
      $this->template->render();  
  }

}

/* End of file stats.php */
/* Location: ./application/controllers/stats.php */
