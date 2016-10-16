<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Public_Controller {

   public function index()
   {
   		$this->template->set_template('single_column');
      	$this->loadGenericAssets();

   		$this->template->write_view('content',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);			
		   $this->template->render();
   }

   public function temp_home()
   {
         //redirect(base_url());

         echo 'Whoops! You shouldn\'t have landed here - please let the IT team know so we can sort it out';
   		//echo "You were looking for the Workflow tool, right?</br> We're shuffling things around a bit, so you'll find it at <a href='/workflow'>Workflow</a> ";

   }

}

/* End of file Controllername.php */
/* Location: ./application/controllers/Controllername.php */