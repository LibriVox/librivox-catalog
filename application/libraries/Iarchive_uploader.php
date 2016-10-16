<?php

class Iarchive_uploader
{

	private $access_key;

	private $secret_key;


	public function __construct($config=null)
	{
		
		if(is_array($config)) $this->initialize($config);
	}

	public function initialize($config){
		if(!is_array($config)) return false;
		
		foreach($config as $key => $val){
			$this->$key = $val;
		}
	}

	
	function curl($params)
	{

		set_time_limit(0);

		//var_dump($params);
		$debug = 1;

		//need to return error message - returns array of missing params
		$key_diff = $this->_validate_param_list($params);
		if (!empty($key_diff)) return false; 

		$authentication = $this->access_key . ':' . $this->secret_key;

		$url = $this->amazon_endpoint. '/'. $params['project_slug']. '/'.$params['filename'];

		$title 			= $params['title'];

		$file_location 	= $params['file_location'];
		$filename		= $params['filename'];
		$full_file_path = $file_location . '/' . $filename;

		if (!is_file($full_file_path)) return 'File error: File not found';

	    $ch = curl_init();

	    if ($debug) curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		//--header ''
		//$headers[] = 'x-archive-ignore-preexisting-bucket:1'; not working - more testing needed

		$headers[] = 'authorization: LOW '. $authentication;
		$headers[] = 'x-amz-auto-make-bucket:1';
		$headers[] = 'x-archive-meta01-collection:librivoxaudio';
		$headers[] = 'x-archive-meta-mediatype:audio';
		$headers[] = 'x-archive-meta-title:' . $title;

		//additional info
		$headers[] = 'x-archive-meta-creator:'.$params['creator'] ;
		$headers[] = 'x-archive-meta-description:'.$params['description'];
		$headers[] = 'x-archive-meta-date:'.$params['date'] ;
		$headers[] = 'x-archive-meta-subject:'.$params['subject'] ;
		$headers[] = 'x-archive-meta-licenseurl:'.$params['licenseurl'];

	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  

	    curl_setopt($ch, CURLOPT_VERBOSE, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");

	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL, $url );

	    curl_setopt($ch, CURLOPT_UPLOAD, 1);
		curl_setopt($ch, CURLOPT_INFILE, fopen($full_file_path, "rb"));
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($full_file_path));

		//most importent curl assumes @filed as file field
	    $post_array = array(
	        "upload-file"=>'@'. $file_location . '/' . $filename
	    );

	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);


	    curl_setopt($ch, CURLOPT_TIMEOUT, 3000);


	    
	    $response = curl_exec($ch);

		if(curl_errno($ch))
		{
		    return 'Curl error: ' . curl_error($ch);
		}
		
		//return curl_getinfo($ch);
		return 'Uploaded';

	}

	function _validate_param_list($params)
	{
		$required_params = array_flip(array('project_slug', 'filename', 'file_location', 'title'));
		return array_diff_key($required_params, $params);
	}
}
