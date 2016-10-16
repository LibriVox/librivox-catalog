<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blue_imp
{
	public function __construct()
	{
				
		//Set relative Path with CI Constant
        //$this->setPath_img_upload_folder( './' . DIR_VALIDATOR);
        $this->setPath_img_thumb_upload_folder("assets/img/articles/thumbnails/");

        
		//Delete img url
        $this->setDelete_img_url(base_url() . 'uploader/uploader/deleteImage/');  
 

		//Set url img with Base_url()
        $this->setPath_url_img_upload_folder(base_url() . "assets/img/articles/");
        $this->setPath_url_img_thumb_upload_folder(base_url() . "assets/img/articles/thumbnails/");
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