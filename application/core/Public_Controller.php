<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
  * Public_Controller - Base Public Controller for Librivox
  * This is where the home page, etc is located - it is a base class
  * It does NOT require login
  */

class Public_Controller extends MY_Controller {

	public function __construct()
	{			
		parent::__construct();
		
		$this->base_path = 'public';


		
		//$this->template->add_css('css/double_column.css');		
	
		$this->template->write_view('header', 'common/public_header', $this->data);
		$this->template->write_view('bottom_tagline', 'common/public_bottom_tagline');
		$this->template->write_view('footer', 'common/public_footer');
		
	}
}