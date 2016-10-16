<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Genre_manager extends Private_Controller {

	public function __construct()
	{			
		parent::__construct();

		$this->base_path = 'admin';

		$this->load->library('Mahana_hierarchy');
		$this->load->model('genre_model');

	}


	public function index()
	{
		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);


		$this->data['genres'] = $this->mahana_hierarchy->get();

		$this->data['genre_dropdown'][0] = 'Top Level';
		foreach($this->data['genres'] as $genre)
		{
			$this->data['genre_dropdown'][$genre['id']] = $genre['name'];
		}	

		$this->load->helper('form');

		$this->insertMethodJS();

   		$this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);			
		$this->template->render();
		
	}

	public function update_genre()
	{
		$fields = $this->input->post(null, true);

		if ($fields['id'])
		{
			$this->genre_model->update($fields['id'],$fields);
		}	
		else
		{
			$this->genre_model->insert($fields);
		}	
		

		$this->mahana_hierarchy->resync();

		$this->ajax_output(array('fields'=>$fields) , TRUE , FALSE);

	}

}

/* End of file genre_manager.php */
/* Location: ./application/controllers/genre_manager.php */