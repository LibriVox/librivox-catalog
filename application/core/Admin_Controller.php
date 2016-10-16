<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
  * Admin_Controller - Base Admin Controller for Librivox
  * This is NOT where the admin dashboard, etc is located - it is a base class
  */

class Admin_Controller extends MY_Controller {

	public function __construct()
	{			
		parent::__construct();
		
		if (!$this->ion_auth->is_logged_in()) {
			redirect(base_url().'login/');
		} 
		
		$allowed_groups = array(PERMISSIONS_ADMIN);
		if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			echo 'No permission!';
			redirect('auth/login');
		}
		
		$this->base_path = 'admin';

		$this->template->set_template('admin');
		
		// setup generic template stuff
		$this->loadGenericAssets();
		
		$this->template->add_css('css/admin.css');

		
		$this->template->write_view('header', 'admin/admin_header');
		$this->template->write_view('footer', 'common/public_footer');		
	}
}