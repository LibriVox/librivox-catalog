<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Uploader extends Public_Controller {

    protected $path_img_upload_folder;
    protected $path_img_thumb_upload_folder;
    protected $path_url_img_upload_folder;
    protected $path_url_img_thumb_upload_folder;

    protected $delete_img_url;

  	function __construct() {
        parent::__construct();

        set_time_limit(0);
        ini_set('memory_limit', '128M');

        $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_BCS, PERMISSIONS_MCS, PERMISSIONS_UPLOADER, PERMISSIONS_PLS, PERMISSIONS_READERS);
        if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
        {
            redirect('auth/error_no_permission');
        }


        $this->load->helper(array('form', 'url'));

        $this->base_path = 'uploader';

		//Set relative Path with CI Constant
        $this->setPath_img_upload_folder("./uploads/");
        $this->setPath_img_thumb_upload_folder("assets/img/articles/thumbnails/");

        
		//Delete img url -- THIS IS NOT DELETE. We are reusing this to get a clean display path for copy/paste
        $this->setDelete_img_url(base_url() . 'uploads/');  
 

		//Set url img with Base_url()
        $this->setPath_url_img_upload_folder(base_url() . "assets/img/articles/");
        $this->setPath_url_img_thumb_upload_folder(base_url() . "assets/img/articles/thumbnails/");
  	}

  	public function index() {

        $this->load->config('librivox');
        $this->data['languages'] = $this->config->item('languages');
        
        $this->data['current_lang'] = $this->session->userdata('lang_code');

  		$this->load->model('user_model');
  		$this->data['mcs'] = $this->user_model->get_dropdown_by_role('mc', true, false);
        asort($this->data['mcs'], SORT_NATURAL | SORT_FLAG_CASE);

        $this->data['mcs'] += array('-----'=>'-----','tests'=>'tests', 'covers'=>'covers', 'bloopers'=>'bloopers', 'xx-nonproject'=>'xx-nonproject');
        
      	
  		$this->template->add_css('css/bootstrap.css');
  		$this->template->add_css('css/uploader/jquery.fileupload-ui.css');
  		$this->template->add_css('css/common/style.css');

  		$this->template->add_js('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', 'external');
  		$this->template->add_js('js/uploader/jquery.ui.widget.js');
  		//$this->template->add_js('//blueimp.github.com/JavaScript-Templates/tmpl.min.js', 'external');
        $this->template->add_js('js/uploader/tmpl.min.js');

		$this->template->add_js('js/bootstrap.min.js');
		//$this->template->add_js('//blueimp.github.com/Bootstrap-Image-Gallery/js/bootstrap-image-gallery.min.js', 'external');
		$this->template->add_js('js/uploader/jquery.iframe-transport.js');
		$this->template->add_js('js/uploader/jquery.fileupload.js');
		$this->template->add_js('js/uploader/jquery.fileupload-fp.js');
		$this->template->add_js('js/uploader/jquery.fileupload-ui.js');
		$this->template->add_js('js/uploader/main.js');

      	$this->template->write_view('content_left',$this->base_path.'/'.build_view_path(__METHOD__), $this->data);			
		$this->template->render();
  	}

	// Function called by the form
  	public function upload_file() {

  		if ($_POST)
  		{
	  		$mc = $this->input->post('mc');
	  		if (empty($mc))
	  		{
	  			$error = array('error' => 'You must select an MC for the project');
	            echo json_encode(array($error));return;
	  		}

	        //Format the name
	        $name = $_FILES['userfile']['name'];
	        $name = strtr($name, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');

	        // replace characters other than letters, numbers and . by _
            $name = preg_replace('/^[a-z0-9A-Z_\-]+$/', '_', $name);

	        //Your upload directory, see CI user guide
	        $config['upload_path'] = $this->getPath_img_upload_folder().$mc;	  
	        $config['allowed_types'] = 'jpg|mp3|wav|txt|flac|zip';  //.mp3, .wav, .txt, .flac and .zip
	        $config['max_size'] = '300000000';
	        $config['file_name'] = $name;

            //make dir if it doesn't exist
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path']);
                chmod($config['upload_path'], 0777); 
            }


	       //Load the upload library
	        $this->load->library('upload', $config);

	        if ($this->do_upload()) {

	            // Codeigniter Upload class alters name automatically (e.g. periods are escaped with an
	            //underscore) - so use the altered name for thumbnail
	            $data = $this->upload->data();
	            $name = $data['file_name'];

	            /*
	            //If you want to resize 
	            $config['new_image'] = $this->getPath_img_thumb_upload_folder();
	            $config['image_library'] = 'gd2';
	            $config['source_image'] = $this->getPath_img_upload_folder() . $name;
	            $config['create_thumb'] = FALSE;
	            $config['maintain_ratio'] = TRUE;
	            $config['width'] = 193;
	            $config['height'] = 94;

	            $this->load->library('image_lib', $config);

	            $this->image_lib->resize();
	            */

	            //Get info 
	            $info = new stdClass();
	            
	            $info->name = $name;
	            $info->size = $data['file_size'];
	            $info->type = $data['file_type'];
	            $info->url = $this->getPath_img_upload_folder() . $mc .'/'.$name;
	            //$info->thumbnail_url = $this->getPath_img_thumb_upload_folder() . $name; //I set this to original file since I did not create thumbs.  change to thumbnail directory if you do = $upload_path_url .'/thumbs' .$name
	            $info->delete_url = $this->getDelete_img_url() . $mc .'/'.$name;
	            $info->delete_type = 'DELETE';


	           //Return JSON data
	           if (IS_AJAX) {   //this is why we put this in the constants to pass only json data
	                echo json_encode(array($info));
	                //this has to be the only the only data returned or you will get an error.
	                //if you don't give this a json array it will give you a Empty file upload result error
	                //it you set this without the if(IS_AJAX)...else... you get ERROR:TRUE (my experience anyway)
	            } 
	            else 
	            {   // so that this will still work if javascript is not enabled
	                $file_data['upload_data'] = $this->upload->data();
	                echo json_encode(array($info));
	            }
	        } 
	        else 
	        {

	           // the display_errors() function wraps error messages in <p> by default and these html chars don't parse in
	           // default view on the forum so either set them to blank, or decide how you want them to display.  null is passed.
	            $error = array('error' => $this->upload->display_errors('',''));
	            echo json_encode(array($error));
	        }

        }

    }

	//Function for the upload : return true/false
  	public function do_upload() {

        if (!$this->upload->do_upload()) {

            return false;
        } else {
            //$data = array('upload_data' => $this->upload->data());

            return true;
        }
    }


	//Function Delete image
    public function deleteImage() {

        // DEACTIVATING DELETE

        echo json_encode(array('success'=>true));
        return;

        /*
        //librivox hack - get the MC dir
        $mc = $this->uri->segment(4);
        //Get the name in the url
        $file = $this->uri->segment(5);
        
        $success = unlink($this->getPath_img_upload_folder() . $mc. '/'. $file);
        //$success_th = unlink($this->getPath_img_thumb_upload_folder() . $file);

        //info to see if it is doing what it is supposed to	
        $info = new stdClass();
        $info->sucess = $success;
        $info->path = $this->getPath_url_img_upload_folder() . $file;
        $info->file = is_file($this->getPath_img_upload_folder() . $file);
        if (IS_AJAX) {//I don't think it matters if this is set but good for error checking in the console/firebug
            echo json_encode(array($info));
        } else {     //here you will need to decide what you want to show for a successful delete
            var_dump($file);
        }
        */
    }


	//Load the files
    public function get_files() {

        $this->get_scan_files();
    }

	//Get info and Scan the directory
    public function get_scan_files() {

        $file_name = isset($_REQUEST['file']) ?
                basename(stripslashes($_REQUEST['file'])) : null;
        if ($file_name) {
            $info = $this->get_file_object($file_name);
        } else {
            $info = $this->get_file_objects();
        }
        header('Content-type: application/json');
        echo json_encode($info);
    }

    protected function get_file_object($file_name) {
        $file_path = $this->getPath_img_upload_folder() . $file_name;
        if (is_file($file_path) && $file_name[0] !== '.') {

            $file = new stdClass();
            $file->name = $file_name;
            $file->size = filesize($file_path);
            $file->url = $this->getPath_url_img_upload_folder() . rawurlencode($file->name);
            $file->thumbnail_url = $this->getPath_url_img_thumb_upload_folder() . rawurlencode($file->name);
            //File name in the url to delete 
            $file->delete_url = $this->getDelete_img_url() . rawurlencode($file->name);
            $file->delete_type = 'DELETE';
            
            return $file;
        }
        return null;
    }

	//Scan
       protected function get_file_objects() {
        return array_values(array_filter(array_map(
             array($this, 'get_file_object'), scandir($this->getPath_img_upload_folder())
                   )));
    }



	// GETTER & SETTER 

    public function getPath_img_upload_folder() {
        return $this->path_img_upload_folder;
    }

    public function setPath_img_upload_folder($path_img_upload_folder) {
        $this->path_img_upload_folder = $path_img_upload_folder;
    }

    public function getPath_img_thumb_upload_folder() {
        return $this->path_img_thumb_upload_folder;
    }

    public function setPath_img_thumb_upload_folder($path_img_thumb_upload_folder) {
        $this->path_img_thumb_upload_folder = $path_img_thumb_upload_folder;
    }

    public function getPath_url_img_upload_folder() {
        return $this->path_url_img_upload_folder;
    }

    public function setPath_url_img_upload_folder($path_url_img_upload_folder) {
        $this->path_url_img_upload_folder = $path_url_img_upload_folder;
    }

    public function getPath_url_img_thumb_upload_folder() {
        return $this->path_url_img_thumb_upload_folder;
    }

    public function setPath_url_img_thumb_upload_folder($path_url_img_thumb_upload_folder) {
        $this->path_url_img_thumb_upload_folder = $path_url_img_thumb_upload_folder;
    }

    public function getDelete_img_url() {
        return $this->delete_img_url;
    }

    public function setDelete_img_url($delete_img_url) {
        $this->delete_img_url = $delete_img_url;
    }


}