<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Temp_info extends Public_Controller {

	public function index()
	{
		
	}

	public function api()
	{

   		$this->template->set_template('single_column');
      	$this->loadGenericAssets();

   		$this->template->write_view('content',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);			
		$this->template->render();	
	}
}

/* End of file temp_info.php */
/* Location: ./application/controllers/temp_info.php */