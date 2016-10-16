<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sections extends Catalog_controller {




  	public function readers($user_id = 0)
  	{

      	if(!$user_id) redirect(base_url());


		$this->data['results'] = array();	

		$this->data['search_category'] = 'sections';

		$this->data['primary_key'] = $user_id;		


		$this->load->model('user_model');
		$this->data['reader'] = $this->user_model->get($user_id);		


		// page specific code

      	$this->load->model('section_model');

      	$projects = $this->section_model->get_sections_by_reader($user_id, 'reader');
      	//echo $this->db->last_query();

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


		$this->load->model('section_readers_model');
		$sections = $this->section_readers_model->get_many_by('reader_id', $user_id);
		$this->data['sections'] = count($sections);

		$matches = $this->_get_all_reader($user_id);
		$this->data['matches'] = count($matches);



		$this->_render('catalog/sections');
		return;

    }

  	//heads up - this is a copy of the one on Section controller. long term would be good to put all of these onLibrivox_search lib, or models 
	function _get_all_reader($reader_id, $offset=0, $limit=1000000, $search_order = 'alpha', $project_type = 'either')
	{
		$params['reader_id'] = $reader_id;
		$params['offset'] 	= $offset;
		$params['limit'] 	= $limit;
		$params['search_order'] 	= $search_order;
		$params['project_type'] 	= $project_type;

		$this->load->model('project_model', 'model');
		return $this->model->get_projects_by_reader($params);

	}



}

/* End of file sections.php */
/* Location: ./application/controllers/sections.php */