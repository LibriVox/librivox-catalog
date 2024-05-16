<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class Catalog_item {

	public $ci;

	public $prototype = array();

	public function __construct($config=null){    
		$this->ci =& get_instance();    

		$this->ci->load->model('project_model');    
		$this->ci->load->model('author_model');    
		$this->ci->load->model('project_author_model'); 
		$this->ci->load->model('section_model');  

	}

	public function alter_data($data)
	{
		$retval = array();

		$project_info = $this->_prep_project_data($data);

		if ($project_info['id'])
		{
			$project_info = $this->_create_project_slug($project_info);
		}			
		

		$project_id = $this->_update_or_insert('id', 'project_model', $project_info);

		$this->_create_sections($project_id, $project_info);
		

		$keyword_ids = $this->_process_keywords($project_id, $data['list_keywords']);

		$genre_ids = $this->_process_genres($project_id, $data['genres']);

		$new_authors = $this->_authors($project_id, $data);
		$new_translators = $this->_translators($project_id, $data);

		$retval['project_id'] 	= $project_id;
		$retval['new_authors'] 	= $new_authors;
		$retval['new_translators'] 	= $new_translators;
		$retval['url_librivox'] 	= $project_info['url_librivox'];

		return $retval;		

	}


    public function _prep_date($date_string)
    {
        // 0000-00-00 appears to break DateTime::createFromFormat (it returns
        // -0001-110-30), special case it.
        if ($date_string == '0000-00-00') {
            return $date_string;
        }
        $d = DateTime::createFromFormat('Y-m-d', $date_string);
        if ($d) {
            return $d->format('Y-m-d');
        }
        return '0000-00-00';
    }


	public function _prep_project_data($data)
	{
		//project table
		$project_info['id'] 			= $data['project_id'];

		$project_info['language_id'] 	= $data['recorded_language'] ? $data['recorded_language'] : 0;
		$project_info['title_prefix'] 	= $data['title_prefix'];
   		$project_info['title'] 			= $data['projectname'];
		$project_info['description'] 	= $data['projectdescription'];
		//$project_info['project_type'] 	= $data['project_type'];    // add to form
		$project_info['num_sections'] 	= $data['nsections'] ? $data['nsections'] : 0;
		$project_info['has_preface'] 	= empty($data['firstsection']) ? 1 : 0;
		$data['totaltime'] 				= $data['totaltime'];

		$project_info['status']		 	= $data['status'];
		$project_info['project_type']	= $data['project_type'];
		$project_info['is_compilation']	= isset($data['is_compilation'])? 1 :0 ;
		$project_info['totaltime']		= $data['totaltime'];
		$project_info['zip_size']		= $data['zip_size'];


        $project_info['date_begin']		= $this->_prep_date($data['begindate']);
        $project_info['date_catalog']	= $this->_prep_date($data['catalogdate']);
        $project_info['date_target']	= $this->_prep_date($data['targetdate']);

		$project_info['copyright_year']	= $data['copyrightyear'] ? $data['copyrightyear'] : NULL;
		$project_info['copyright_check']= (empty($data['copyrightcheck']))? 0: 1;;

		$project_info['url_librivox']	= $data['librivoxurl'];
		$project_info['url_forum']		= $data['forumurl'];
		$project_info['url_iarchive']	= $data['archiveorgurl'];
		$project_info['url_text_source']= $data['gutenburgurl'];
		$project_info['url_project']	= $data['wikibookurl'];   /// check this
		$project_info['zip_url']		= $data['zip_url']; 

		//volunteers
		$project_info['person_bc_id']		= $data['person_bc_id'] ? $data['person_bc_id'] : 0;
		$project_info['person_altbc_id']	= empty($data['person_altbc_id']) ? 0 : $data['person_altbc_id'];
		$project_info['person_mc_id']		= $data['person_mc_id'] ? $data['person_mc_id'] : 0;
		$project_info['person_pl_id']		= $data['person_pl_id'] ? $data['person_pl_id'] : 0;
		
		$project_info['notes']		 	= $data['notes'];

		$project_info['coverart_pdf']		 	= $data['coverart_pdf'];
		$project_info['coverart_jpg']		 	= $data['coverart_jpg'];
		$project_info['coverart_thumbnail']		= $data['coverart_thumbnail'];

		return $project_info;

	}

	private function _create_project_slug($project_info)
	{
		// if we are marking "complete" we need to create the slug
		if ($project_info['status'] == PROJECT_STATUS_VALIDATION && empty($project_info['url_librivox']))
		{
			$config = array(
			    'field' => 'url_librivox',
			    'title' => 'title',
			    'table' => 'projects',
			    'id' => 'id',
			    'base_url' => 'https://librivox.org/', //replace with a config from somewhere
			);
			$this->ci->load->library('slug', $config);

			//we'd better have an author at this point, but we don't know for sure. We'll add if possible
			$author_tag = '';
			if ($project_info['id'])
			{
				$authors = $this->ci->project_model->get_project_authors($project_info['id']);
				if (!empty($authors))
				{
					$this->ci->load->helper('previewer_helper');
					foreach($authors as $author )
					{
						$author_tag = ' by ' . $author->author;
						break; // we only want the first, but function returns result() not $query so have to cludge it a bit
					}	
					
					
				}	
				
			}	


			$data = array(
			    'title' =>  trim($project_info['title_prefix']. ' '. $project_info['title']. $author_tag),
			);
			$project_info['url_librivox'] =  $this->ci->slug->create_uri($data);

			
		}	

		return $project_info;
	}


	private function _update_or_insert($primary_key, $model, $data)
	{
		$this->ci->load->model($model, 'model');
		if (!empty($data[$primary_key]))
		{
			$this->ci->model->update($data[$primary_key] ,$data);
			$primary_key = $data[$primary_key];
		}	
		else
		{
			$primary_key = $this->ci->model->insert($data);
		}	
		return $primary_key;
		
	}

	private function _create_sections($project_id, $project_info)
	{
		if (!empty($project_info['id'])) return false;

		$firstsection = (empty($project_info['has_preface'])) ? 1 : 0;

		for ($i = $firstsection; $i <= ($project_info['num_sections'] - $project_info['has_preface']); $i++)
		{
			$this->ci->section_model->insert(array('project_id'=>$project_id, 'section_number'=>$i));
		}	

	}

	private function _process_keywords($project_id, $keywords_tag)	
	{
		$this->ci->load->library('keywords'); 
		$this->ci->load->model('project_keyword_model');   

		$keyword_ids = $this->ci->keywords->process($keywords_tag);

		//replace keywords with new links
		$this->ci->project_keyword_model->delete_by('project_id', $project_id);

		$keyword_ids = explode(',', $keyword_ids);
		foreach ($keyword_ids as $keyword_id) {
            if ($keyword_id) {
                $this->ci->project_keyword_model->insert(array('project_id'=>$project_id, 'keyword_id'=>$keyword_id));
            } else {
                $this->ci->project_keyword_model->insert(array('project_id'=>$project_id));
            }
		}
		// For keywords used in this project, update 'instances' field of 'keywords' table now,
                // so correct keyword instances stats can be shown on catalog page even before keywords cron stats job runs. 
                $this->ci->project_keyword_model->set_keywords_statistics_by_project($project_id);

	}

	private function _process_genres($project_id, $genre_ids)	
	{

		$this->ci->load->model('project_genre_model');   

		//replace keywords with new links
		$this->ci->project_genre_model->delete_by('project_id', $project_id);

		$genre_id_array = explode(',',$genre_ids);
		foreach ($genre_id_array as $genre_id) {
            if ($genre_id) {
                $this->ci->project_genre_model->insert(array('project_id'=>$project_id, 'genre_id'=>$genre_id));
            } else {
                $this->ci->project_genre_model->insert(array('project_id'=>$project_id));
            }
		}

	}

	function _authors($project_id, $data)
	{

		//delete all existing links so we can recreate
		$this->ci->project_author_model->delete_by(array('project_id'=>$project_id, 'type'=>'author'));

		if (empty( $data['auth_id'])) return array();

		//collect only the data fields we want to work with
		$author_array = array();
		$author_array['id'] 			= $data['auth_id'];
		$author_array['first_name'] 	= $data['auth_first_name'];
		$author_array['last_name'] 		= $data['auth_last_name'];
		$author_array['dob'] 			= $data['auth_yob'];
		$author_array['dod'] 			= $data['auth_yod'];
		$author_array['author_url'] 	= $data['link_to_auth'];

		if(empty($author_array)) return array();

		$new_authors = array();

		$array_keys = array_keys($author_array['id']);
		foreach ($array_keys as $i) {
			
			if ($author_array['id'][$i])
			{

				$author['first_name']	= $author_array['first_name'][$i];
				$author['last_name'] 	= $author_array['last_name'][$i] ;
				$author['dob'] 			= $author_array['dob'] [$i]		;
				$author['dod'] 			= $author_array['dod'] [$i]		;
				$author['author_url']	= $author_array['author_url'][$i];

				$this->ci->author_model->update($author_array['id'][$i], $author);

				//author exists - just link
				$this->ci->project_author_model->insert(array('project_id'=>$project_id, 'author_id'=>$author_array['id'][$i], 'type'=>'author'));
			}	
			else
			{
				//create author then link

                // Reset $author to empty array. Looks like there's scope
                // persistency weirdness going on, wherein the 'auth_id'
                // field that we set below on L293 persists in subsequent
                // iterations of the loop. As 'auth_id' is not a valid DB
                // field, the insert() on L290 will fail if it's present in the
                // $author array.
                $author = array();
				$author['first_name']	= $author_array['first_name'][$i];
				$author['last_name'] 	= $author_array['last_name'][$i] ;
				$author['dob'] 			= $author_array['dob'] [$i]		;
				$author['dod'] 			= $author_array['dod'] [$i]		;
				$author['author_url']	= $author_array['author_url'][$i];

				$author_id = $this->ci->author_model->insert($author);

				$this->ci->project_author_model->insert(array('project_id'=>$project_id, 'author_id'=>$author_id, 'type'=>'author'));

				//return array of new authors so we can update their auth_ids
				$author['auth_id'] = $author_id;

				$new_authors[] = $author;
			}	

		}

		return $new_authors;

	}


	function _translators($project_id, $data)
	{

		//delete all existing links so we can recreate
		$this->ci->project_author_model->delete_by(array('project_id'=>$project_id, 'type'=>'translator'));

		if (empty( $data['trans_id'])) return array();

		//collect only the data fields we want to work with
		$author_array = array();
		$author_array['id'] 			= $data['trans_id'];
		$author_array['first_name'] 	= $data['trans_first_name'];
		$author_array['last_name'] 		= $data['trans_last_name'];
		$author_array['dob'] 			= $data['trans_yob'];
		$author_array['dod'] 			= $data['trans_yod'];
		$author_array['author_url'] 	= $data['link_to_trans'];

		if(empty($author_array)) return array();


		$new_authors = array();

		$array_keys = array_keys($author_array['id']);
		foreach ($array_keys as $i) {
			
			if ($author_array['id'][$i])
			{

				$author['first_name']	= $author_array['first_name'][$i];
				$author['last_name'] 	= $author_array['last_name'][$i] ;
				$author['dob'] 			= $author_array['dob'] [$i]		;
				$author['dod'] 			= $author_array['dod'] [$i]		;
				$author['author_url']	= $author_array['author_url'][$i];

				$this->ci->author_model->update($author_array['id'][$i], $author);

				//author exists - just link
				$this->ci->project_author_model->insert(array('project_id'=>$project_id, 'author_id'=>$author_array['id'][$i], 'type'=>'translator'));
				
			}	
			else
			{
				//create author then link

				$author['first_name']	= $author_array['first_name'][$i];
				$author['last_name'] 	= $author_array['last_name'][$i] ;
				$author['dob'] 			= $author_array['dob'] [$i]		;
				$author['dod'] 			= $author_array['dod'] [$i]		;
				$author['author_url']	= $author_array['author_url'][$i];

				$author_id = $this->ci->author_model->insert($author);

				$this->ci->project_author_model->insert(array('project_id'=>$project_id, 'author_id'=>$author_id, 'type'=>'translator'));

				//return array of new authors so we can update their auth_ids
				$author['auth_id'] = $author_id;

				$new_authors[] = $author;
			}	

		}

		return $new_authors;

	}


   	public function get_prototype()
   	{
   		$data = array();
      	$data['project_id'] = 0;
      	$data['title_prefix'] = '';
   		$data['projectname'] = '';
		$data['projectdescription'] = '';
		$data['type'] 		= '';
		$data['nsections'] 	= '';
		$data['firstsection'] = '';
		$data['totaltime'] = '';
		$data['zip_size'] = '';
		$data['person_bc_id'] 	= 0;
		$data['person_altbc_id'] = 0;
		$data['person_mc_id'] 	= 0;
		$data['person_pl_id'] 	= 0;
		$data['begindate'] 	= '';
		$data['catalogdate'] = '';
		$data['targetdate'] = '';
		$data['status'] = '';
		$data['copyrightyear'] = '';
		$data['copyrightcheck'] = '';
		$data['librivoxurl'] = '';
		$data['forumurl'] = '';
		$data['archiveorgurl'] = '';
		$data['gutenburgurl'] = '';
		$data['wikibookurl'] = '';
		$data['zip_url'] = '';
		$data['recorded_language'] = 1; 			
		$data['genre'] = '';
		$data['notes'] = '';
		$data['coverart_pdf'] = '';
		$data['coverart_jpg'] = '';
		$data['coverart_thumbnail'] = '';
		return $data;
   }

}
