<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Feed extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('Librivox_API');
	}



	public function audiobooks_get()
	{

		$params['offset'] 	= $this->get('offset');
		$params['limit'] 	= $this->get('limit');
		$params['id'] 		= $this->get('id');

		$params['since'] 	= $this->get('since');
		$params['extended'] = $this->get('extended');
		//$params['simple'] 	= $this->get('simple');
		$params['genre'] 	= $this->get('genre');
		$params['title'] 	= $this->get('title');
		$params['author'] 	= $this->get('author');

		$params['fields'] 	= $this->get('fields');


		// format already listened for

		$audiobooks = $this->librivox_api->get_audiobooks($params);

		//var_dump($audiobooks);return;

        if($audiobooks)
        {
            $this->response($audiobooks, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Audiobooks could not be found'), 404);
        }
		
	}


	public function audiotracks_get()
	{
		$params['id'] 			= $this->get('id');
		$params['project_id'] 	= $this->get('project_id');	

		$audiotracks = $this->librivox_api->get_audiotracks($params);

		//var_dump($audiotracks);return;

        if($audiotracks)
        {
            $this->response($audiotracks, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Audiotracks could not be found'), 404);
        }	
	}

	public function authors_get()
	{
		$params['offset'] 		= $this->get('offset');
		$params['limit'] 		= $this->get('limit');

		$params['id'] 			= $this->get('id');
		$params['last_name'] 	= $this->get('last_name');	

		$audiotracks = $this->librivox_api->get_authors($params);

		//var_dump($audiotracks);return;

        if($audiotracks)
        {
            $this->response($audiotracks, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Audiotracks could not be found'), 404);
        }	
	}	


	public function lastest_releases_get()
	{
		$this->load->model('project_model');
		$projects = $this->project_model->get_lastest_releases(10);


        if($projects)
        {

			foreach ($projects as $key => $project) {
				$projects[$key]->authors = $this->project_model->get_project_authors($project->id);
			}
            $this->response($projects, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Latest projects could not be found'), 404);
        }	

	}

	public function statistics_get()
	{
		$this->load->model('statistics_model');
		$statistics = $this->statistics_model->order_by('updated', 'desc')->limit(1)->get_all();

        if($statistics)
        {
            $this->response($statistics[0], 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Statistics could not be found'), 404);
        }	
		

	}	
}

/* End of file aPI.php */
/* Location: ./application/controllers/aPI.php */
