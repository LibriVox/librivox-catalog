<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Language_manager extends Private_Controller {

	public function __construct()
	{			
		parent::__construct();

		$this->base_path = 'admin';

		$this->load->model('language_model');

      	$this->template->add_css('css/libs/datatable.css');
      	$this->template->add_js('js/libs/jquery.dataTables.js');
		$this->template->add_js('js/libs/jquery.jeditable.js');

	}

	public function index()
	{
		ini_set('memory_limit', '-1'); //we need to see about chunking this

		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		$this->data['languages']	= $this->language_model->order_by('common', 'desc')->order_by('language', 'asc')->get_all(); //

		$this->insertMethodCSS();
    	$this->insertMethodJS();

   		$this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);			
		$this->template->render();
		
	}


    public function update_language_value()
    {
    	$id = $this->input->post('id', true);
    	$value = $this->input->post('value', true);

    	list($field, $language_id) = explode('-',$id);

    	$this->language_model->update($language_id, array($field=>trim($value)));

    	echo $value; return;
    	//$this->ajax_output(array('value'=>$value) , TRUE , FALSE);

    }

    public function add_language()
    {
    	$fields = $this->input->post(null, true);
    	$insert_id = $this->language_model->insert($fields);

    	echo $insert_id; return;
    }

}

/* End of file language_manager.php */
/* Location: ./application/controllers/language_manager.php */