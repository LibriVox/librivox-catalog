<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Controller extends CI_Controller {

	public $data = array();

	public $ajax_debug;

	public function __construct()
	{				
		parent::__construct();
        $lang_code = ($this->session->userdata('lang_code'))? $this->session->userdata('lang_code'):'english';
        $this->lang->load('project_launch', $lang_code);
        $this->lang->load('project_launch_template', $lang_code);
        $this->lang->load('project_launch_uploader', $lang_code);	

        $this->ajax_debug = false;

        $this->data['user_id'] = $this->librivox_auth->get_user_id();
	}
	
	
    function lang_select(){
        $lang_code = $this->input->post('lang_code');
        $this->session->set_userdata('lang_code', $lang_code );
    }
	
	
	/**
	 * Ajax handler for ajax output
	 */	
	public function ajax_output($data , $status = TRUE , $js_eval = FALSE)
	{
		//Set Header
		//header('Content-Type: application/json');
		header('Content-Type: application/json;charset=utf-8');

		//Set Return array
		$return = array();
		$return['data'] = $data;
		$return['status'] = ($status ? "SUCCESS" : "FAIL" );
		$return['js_eval'] = ( ! empty($js_eval) ? $js_eval : "" );

		//Check for debugging
		if($this->ajax_debug){
			if(isset($this->db)){
				$return['debug_sql'] = $this->db->last_query();
			}
			if(isset($this->session)){
				$return['debug_session'] = $this->session->userdata();
			}
		}

		//Output and die
		echo json_encode($return); die();
	}

	/**
	 * This should be called right after a different template is set using 
	 * $this->template->set_template('template name'). The reason for this is that 
	 * the array containing the CSS/JS references is cleared when the template is set, 
	 * so setting CSS/JS files before that will not work as the Template library assumes 
	 * you will use completely different CSS/JS files for the new template, but we know 
	 * we need to have some generics JS files(like jQuery) and CSS(like HTML5 Boilerplate) 
	 * style sheet on all pages 
	 */
	public function loadGenericAssets() {
		//template stuff

      	$this->template->add_js('js/libs/jquery-1.8.2.js'); //.min
		$this->template->add_js('js/libs/bootstrap.js');
		$this->template->add_js('js/libs/jquery.validate.js');
        
        $this->template->add_js('js/libs/jquery-ui-1.8.24.custom.min.js');

        $this->template->add_js('js/common/autocomplete.js');
		$this->template->add_js('js/common/application.js');
		
		$this->template->add_css('css/bootstrap.css');
		$this->template->add_css('css/bootstrap-responsive.css');
		$this->template->add_css('css/ui-lightness/jquery-ui-1.8.24.custom.css');
		$this->template->add_css('css/common/style.css');

		//$this->template->add_js('js/common/jquery.tagsinput.min.js');
		//$this->template->add_css('css/common/jquery.tagsinput.css');

	}
	
	/**
	 * Insert the CSS file specific to the current method
	 */
	public function insertMethodCSS() {
		list(, $caller) = debug_backtrace(false);
		
		$path[] = 'css';
		
		if (isset($this->base_path) && !empty($this->base_path))
			$path[] = $this->base_path;
			
		$path[] = strtolower($caller['class']);	
		$path[] = $caller['function'] . '.css';	
		
		$this->template->add_css(implode('/', $path));
	}
	
	/**
	 * Insert the JS file specific to the current method
	 */
	public function insertMethodJS() {
		list(, $caller) = debug_backtrace(false);
		
		$path[] = 'js';
		
		if (isset($this->base_path) && !empty($this->base_path))
			$path[] = $this->base_path;
			
		$path[] = strtolower($caller['class']);	
		$path[] = $caller['function'] . '.js';	
		
		$this->template->add_js(implode('/', $path));
	}
}