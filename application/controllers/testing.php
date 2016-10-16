<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class testing extends CI_Controller {

		/*
		$params['offset'] 	= $this->get('offset');
		$params['limit'] 	= $this->get('limit');
		$params['id'] 		= $this->get('id');

		$params['since'] 	= $this->get('since');

		$params['genre'] 	= $this->get('genre');
		$params['title'] 	= $this->get('title');
		$params['author'] 	= $this->get('author');

		$params['extended'] = $this->get('extended');
		$params['simple'] 	= $this->get('simple');
		*/
	public function __construct()
	{
		parent::__construct();
		$this->load->library('Librivox_API');
	}

	public function test_cli($to = 'World')
	{
		echo ENVIRONMENT;
		echo "Hello {$to}!".PHP_EOL;
	}

	function test_dropdown()
	{
		echo 'TEST';
		$country_options = array(0=>'ALL', 'Australia'=>'Australia', 'Ruritania'=>'Ruritania');
		var_dump($country_options);

		$this->load->helper('form_helper');
		echo form_dropdown('country', $country_options, 0);

	}


	public function test_filecheck()
	{
		$this->load->model('project_model');
		$this->load->library('librivox_filecheck');

        $project = $this->project_model->get(56);

        $map = array();
        if (!empty($project->validator_dir))
        {
            $dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
            $map = $this->_get_dir_contents($dir);
        } 

        $this->librivox_filecheck->initialize(array('file_array'=>array('tracknumber')));
        $results = $this->librivox_filecheck->load_project_map($dir, $map, $project);

        var_dump($results);

	}


	public function test_fileadjust()
	{
		$this->load->model('project_model');
		$this->load->library('librivox_mp3gain');

        $project = $this->project_model->get(56);

        $map = array();
        if (!empty($project->validator_dir))
        {
            $dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
            $map = $this->_get_dir_contents($dir);
        } 

        var_dump($map);

        $this->librivox_mp3gain->adjust($map);

	}





    function _get_dir_contents($dir)
    {
    	$this->load->helper('directory');
		if(!is_dir($dir)) return false;
		return directory_map( $dir);
    }



	public function test_sphinx()
	{
		$this->load->library('Sphinx/sphinxsearch', 'sphinxsearch');

		//$this->sphinxsearch->set_filter('project_id',array(59));
		$return = $this->sphinxsearch->as_array()->query( '@title Finn', 'projects' );

		var_dump($return);





	}

	public function test_result()
	{

		$results = $this->db->like( 'title', 'booger' )->get('projects')->result_array();

		var_dump($results);

	}

	public function test_project_titles()
	{
		$params['author_id'] = 0;  //all authors
		$params['offset'] 	= 0;
		$params['limit'] 	= 1000000;
		$params['sub_category'] = 'language'; 
		$params['primary_key'] 	= 2; 

		var_dump($params);

		$this->load->model('project_model', 'model');
		$this->model->get_projects_by_author($params);

		echo $this->db->last_query();

	}

	public function test_advanced_search()
	{
		$params['author'] = 'Twain';  //all authors
		$params['title'] = 'Twain';
		$params['reader'] = 'Twain';

		$params['recorded_language'] = 1; 
		$params['status'] 		= 'all'; 
		$params['project_type'] = 'either';

		$params['offset'] 	= 0;
		$params['limit'] 	= 1000000;
		$params['sort_order'] 	= 'alpha';

		var_dump($params);

		$this->load->library('Librivox_search');
		$this->librivox_search->advanced_search($params);

		echo $this->db->last_query();

	}	



	public function test_url()
	{
		$url_iarchive = 'http://archive.org/details/progressandpoverty_1307_librivox';

		$this->load->helper('links_helper');
		echo torrent_link($url_iarchive);




	}

	public function test_datatables()
	{

		$this->load->model('author_model');

		$this->template->add_js('js/libs/jquery-1.8.2.min.js');
		$this->template->add_js('js/libs/bootstrap.js');
		$this->template->add_css('css/bootstrap.css');

      	$this->template->add_css('css/libs/datatable.css');
      	$this->template->add_js('js/libs/jquery.dataTables.js');
		$this->template->add_js('js/libs/jquery.jeditable.js');

		$this->template->add_js('js/test/test_datatables.js');


		$this->data['authors']	= $this->author_model->limit(10)->order_by('id', 'asc')->get_all(); //->limit(5)


   		$this->template->write_view('content_left', 'test/test_datatables', $this->data);			
		$this->template->render();

	}


	public function test_email()
	{

		$this->load->library('email');

		$this->email->from('jeff@example.com', 'Jeff');
		$this->email->to('jrmadsen67@gmail.com');


		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		$this->email->send();

		echo $this->email->print_debugger();



	}


	public function index()
	{
		$params['offset'] 	= false;
		$params['limit'] 	= false;
		$params['id'] 		= 52;

		$params['since'] 	= false;

		$params['genre'] 	= false;
		$params['title'] 	= false;
		$params['author'] 	= false;

		$params['extended'] = false;
		//$params['simple'] 	= false;

		$params['fields']   = "{authors}";


		$result = $this->librivox_api->get_audiobooks($params);


		var_dump($result);
	}

	function test_curl()
	{
		//load config outside of the library so library can be framework agnostic
		$this->load->config('iarchive_uploader');
		$config = $this->config->item('iarchive_uploader');
		$this->load->library('Iarchive_uploader', $config);

		$params = array();

		$params['project_slug'] = 'jefftest_'.$fields['upload_name'];
		$params['title'] 		= $fields['upload_title'];		
		$params['file_location']= $dir;

		foreach ($files as $key=>$filename)
		{
			$params['filename']		= $filename;
			$this->iarchive_uploader->curl($params);
		}	
		

		$params['project_slug'] = 'jeffs_test4_1301_librivox';
		$params['title'] = 'jeffs test4 1301 librivox';
		$params['filename']		= 'francesbaird.mp3';
		$params['file_location']= 'C:/UniServer/www/ias3upload/librivox_test/francesbaird.mp3';
		$this->iarchive_uploader->curl($params);
		
	}


/*
  curl_easy_setopt(hnd, CURLOPT_INFILESIZE_LARGE, (curl_off_t)64d);
  curl_easy_setopt(hnd, CURLOPT_URL, "http://s3.us.archive.org/jeffs_test_1301_librivox/test-francesbaird3.mp3");
  curl_easy_setopt(hnd, CURLOPT_NOPROGRESS, 1);
  curl_easy_setopt(hnd, CURLOPT_UPLOAD, 1);
  curl_easy_setopt(hnd, CURLOPT_FOLLOWLOCATION, 1);
  curl_easy_setopt(hnd, CURLOPT_USERAGENT, "curl/7.21.1 (i686-pc-mingw32) libcurl/7.21.1 OpenSSL/0.9.8r zlib/1.2.3");
  curl_easy_setopt(hnd, CURLOPT_CAINFO, "C:\Program Files\Git\bin\curl-ca-bundle.crt");
  curl_easy_setopt(hnd, CURLOPT_SSL_VERIFYPEER, 1);
  curl_easy_setopt(hnd, CURLOPT_SSH_KNOWNHOSTS, "c:/Users/JMadsen/_ssh/known_hosts");
  curl_easy_setopt(hnd, CURLOPT_MAXREDIRS, 50);
*/
	function test_curl2()
	{

	    $ch = curl_init();
		

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: LOW 1cX9seE3gEUUtPQL:OM1lXOxlEQdJXvQj'));
	    curl_setopt($ch, CURLOPT_VERBOSE, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	    curl_setopt($ch, CURLOPT_POST, true);

	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL, 'http://s3.us.archive.org/jeffs_test_1301_librivox/test-francesbaird4.mp3' );
		//most importent curl assues @filed as file field
	    $post_array = array(
	        "upload-file"=>'@'. 'C:/UniServer/www/ias3upload/librivox_test/francesbaird4.mp3'
	    );
	    curl_setopt($ch, CURLOPT_UPLOAD, 1);
		curl_setopt($ch, CURLOPT_INFILE, fopen('C:/UniServer/www/ias3upload/librivox_test/francesbaird4.mp3', "rb"));
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize('C:/UniServer/www/ias3upload/librivox_test/francesbaird4.mp3'));
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
	    $response = curl_exec($ch);

		if(curl_errno($ch))
		{
		    echo 'Curl error: ' . curl_error($ch);
		}


		var_dump($response);

	}


	function test_audiotracks()
	{
		$params['id'] 		= false;
		$params['project_id'] 		= 6781;


		$result = $this->librivox_api->get_audiotracks($params);


		var_dump($result);		
	}

	public function test_get()
	{

		//http://librivox.local/testing/test_get/?fields={test1,test2}

		//http://librivox.local/testing/test_get/?fields[]=test1&fields[]=test

		$params = $this->input->get();

		var_dump($params);

		var_dump($params['fields']);

		if (is_array($params['fields']))
		{
			$filter = $params['fields'];
		}
		else
		{
			$filter = explode(",", str_replace(array("{", "}"), array("", ""), $params['fields']));
		}


		$project['test1'] = 'testing1';
		$project['test2'] = 'testing2';
		$project['test3'] = 'testing3';
		$project['test4'] = 'testing4';

		$get_list = array_intersect_key($project, array_flip($filter));

		var_dump($get_list);



	}


	public function test_author($value)
	{
		var_dump(urldecode($value));

		$result = $this->librivox_api->_build_author_project_id_list(urldecode($value));
		var_dump($result);


	}

	public function test_genre($value)
	{
		$result = $this->librivox_api->_build_genre_project_id_list(urldecode($value));
		var_dump($result);

	}

	function test_filenames()
	{

		$project_type = 'project';
		$file_array['file_name'] = 'adventuresoftomsawyer_auntpolly_1.mp3';

		$pattern = ($project_type == 'poem') ? '/^(.+)_.*?\.mp3$/' : '/^(.+)_[\d-]+(.*)\.mp3$/';

		preg_match($pattern, $file_array['file_name'], $matches);  //for poems - 

		var_dump($matches);
		//return (!empty($matches));		
	}

	function test_number_parser()
	{
		$this->load->library('number_list_parser');

		$chapters = '01-03,05';

		$array = $this->number_list_parser->parse_list($chapters);

		var_dump($array);
	}

}

/* End of file testing.php */
/* Location: ./application/controllers/testing.php */