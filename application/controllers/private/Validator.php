<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Validator extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();

		//check permissions
		$allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
		if (!$this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
		{
			if ($this->input->is_ajax_request())
			{
				echo "You have no access permissions to this screen";
				return false; //we should log attempt
			}

			redirect('auth/error_no_permission');
		}

		$this->load->model('project_model');

		$this->load->library('Librivox_id3tag');
		$this->load->library('Librivox_filecheck');
		$this->load->library('Librivox_mp3gain');
		$this->load->helper('previewer_helper');

		$this->template->add_css('css/bootstrap.css');
		$this->template->add_js('js/bootstrap.min.js');

		$this->template->add_css('css/libs/jquery.alerts.css');
		$this->template->add_js('js/libs/jquery.alerts.js');

		$this->load->library('Blue_imp');
		$this->blue_imp->setPath_img_upload_folder('./' . DIR_VALIDATOR);
	}

	public function index($project_id = 0)
	{
		if (empty($project_id))
		{
			$project_id = $this->input->get_post('project_id', true);
		}

		if (empty($project_id))
		{
			redirect(base_url() . 'private/validator/select_project');
		}

		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		$this->data['project'] = $this->project_model->get($project_id);

		if (empty($this->data['project'])) redirect(base_url() . 'private/validator/select_project');

		$this->data['project']->full_title = create_full_title($this->data['project']);
		$this->data['project']->suggested_title = substr(url_title(replace_accents($this->data['project']->full_title), '', true), 0, 39);
		$this->data['project']->author_full_name = $this->_get_author_by_project($project_id);
		$this->data['project']->author_last_name = $this->_get_author_by_project($project_id, 'last');

		//section info
		$this->load->model('section_model');
		$sections = $this->section_model->as_array()->get_many_by(array('project_id' => $project_id));
		if (!empty($sections))
		{
			foreach ($sections as $key => $value)
			{
				$this->librivox_id3tag->sections_array[$value['section_number']] = $value['file_name'];
			}
		}

		// freeze project if date catalog more than 2 weeks old
		$this->data['project']->freeze = false;
		if (!empty($this->data['project']->date_catalog) && $this->data['project']->date_catalog != '0000-00-00')
		{
			$today = new DateTime();
			$today->format('Y-m-d');

			$date_catalog = new DateTime($this->data['project']->date_catalog);
			$interval = $today->diff($date_catalog);

			if ($interval->days > 14) $this->data['project']->freeze = true;
		}

		//debug:
		$this->data['project']->freeze = false;

		$map = array();
		$this->data['files_table'] = '';

		if (!empty($this->data['project']->validator_dir))
		{
			$dir = './' . DIR_VALIDATOR . '/' . $this->data['project']->validator_dir . '/';
			$map = $this->_get_dir_contents($dir);
			$this->data['files_table'] = $this->librivox_id3tag->_create_files_table($dir, $map, $this->data['project']->freeze);
		}

		$this->data['uploader_modal'] = $this->load->view('private/validator/uploader_modal', $this->data, TRUE);

		$this->insertMethodCSS();
		$this->insertMethodJS();

		$this->template->add_css('css/uploader/jquery.fileupload-ui.css');
		$this->template->add_css('css/uploader/style.css');
		$this->template->add_js('js/uploader/jquery.ui.widget.js');
		//$this->template->add_js('//blueimp.github.com/JavaScript-Templates/tmpl.min.js', 'external');
		$this->template->add_js('js/uploader/tmpl.min.js');

		$this->template->add_js('js/uploader/jquery.iframe-transport.js');
		$this->template->add_js('js/uploader/jquery.fileupload.js');
		$this->template->add_js('js/uploader/jquery.fileupload-fp.js');
		$this->template->add_js('js/uploader/jquery.fileupload-ui.js');
		$this->template->add_js('js/uploader/main-validator.js');

		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $this->data);
		$this->template->render();
	}

	public function select_project()
	{
		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		$this->data['statuses'] = $this->config->item('project_statuses');

		$this->data['page'] = 'validator';
		$this->data['usage'] = 'search';
		$this->data['project_search_form'] = $this->load->view('private/common/project_search', $this->data, true);

		$this->template->add_css('css/private/administer_projects/new_project_form.css'); //reuse results styling
		$this->insertMethodJS();

		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $this->data);
		$this->template->render();
	}



	// ajax function

	// when we first create a new librivox file directory for a project to store the final version of the files,
	// this is where we do it
	function save_validator_dir()
	{
		//see if name is unique
		$this->form_validation->set_rules('project_id', 'project_id', 'trim|required|xss_clean');
		$this->form_validation->set_rules('validator_dir', 'Validator directory name', 'trim|required|xss_clean|is_unique[projects.validator_dir]');

		if ($this->form_validation->run() == false) $this->ajax_output(array('message' => 'Sorry, that directory name has already been used'), FALSE);

		$project_id = $this->input->post('project_id');
		$validator_dir = $this->input->post('validator_dir');

		$this->load->model('project_model');

		$project = $this->project_model->get($project_id);

		$validator_dir .= '_' . date('ym');

		if (in_array($project->project_type, array(PROJECT_TYPE_POETRY_WEEKLY, PROJECT_TYPE_POETRY_FORTNIGHTLY)))
		{
			$validator_dir .= '.poem';
		}

		if (!$this->_create_dir($validator_dir)) $this->ajax_output(array('message' => 'Sorry, that directory name is already in use'), FALSE);

		$this->project_model->update($project_id, array('validator_dir' => $validator_dir));

		// Return data
		$this->ajax_output(array('message' => 'OK'), TRUE);
	}

	// Private functions
	function _create_dir($validator_dir)
	{
		$path = './' . DIR_VALIDATOR . "/" . $validator_dir . "/";

		if (is_dir($path)) return false;

		$r = mkdir($path, 0775, TRUE);
        chmod($path, 0775);
        return $r;
		//return true;
	}

	// UPLOADER FUNCTIONS
	// Function called by the form
	// TODO - move & combine with main uplader controller? really hate duplicating
	public function upload_file()
	{
		if ($_POST)
		{
			$project_id = $this->input->post('project_id');
			if (empty($project_id))
			{
				$error = array('error' => 'No project id found');
				echo json_encode(array($error));
				return;
			}

			$project = $this->project_model->get($project_id);

			//Format the name
			$name = $_FILES['userfile']['name'];
			$name = strtr($name, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');

			// replace characters other than letters, numbers and . by _
			$name = preg_replace('/^[a-z0-9A-Z_\-]+$/', '_', $name);

			//Your upload directory, see CI user guide
			$config['upload_path'] = $this->blue_imp->getPath_img_upload_folder() . '/' . $project->validator_dir;
			$config['allowed_types'] = 'jpg|mp3|wav|txt|flac|zip';  //.mp3, .wav, .txt, .flac and .zip
			$config['max_size'] = '300000000';
			$config['file_name'] = $name;
			$config['overwrite'] = TRUE;

			//make dir if it doesn't exist
			if (!is_dir($config['upload_path']))
			{
				mkdir($config['upload_path'], 0775);
                chmod($config['upload_path'], 0775);
			}

			//Load the upload library
			$this->load->library('upload', $config);

			if ($this->upload->do_upload())
			{
				// Codeigniter Upload class alters name automatically (e.g. periods are escaped with an
				//underscore) - so use the altered name for thumbnail
				$data = $this->upload->data();
				chmod($data['full_path'], 0664);
				$name = $data['file_name'];

				//Get info
				$info = new stdClass();

				$info->name = $name;
				$info->size = $data['file_size'];
				$info->type = $data['file_type'];
				$info->url = $this->blue_imp->getPath_img_upload_folder() . '/' . $project->validator_dir . '/' . $name;
				//$info->thumbnail_url = $this->getPath_img_thumb_upload_folder() . $name; //I set this to original file since I did not create thumbs.  change to thumbnail directory if you do = $upload_path_url .'/thumbs' .$name
				$info->delete_url = $this->blue_imp->getDelete_img_url() . '/' . $project->validator_dir . '/' . $name;
				$info->delete_type = 'DELETE';

				//we need to run our meta update on the

				//Return JSON data
				if (IS_AJAX)
				{   //this is why we put this in the constants to pass only json data
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
				$error = array('error' => $this->upload->display_errors('', ''));
				echo json_encode(array($error));
			}
		}
	}

	// The "copy from section compiler" function
	// when we copy, we also write the id3tags & then read them to display in the table
	function copy_project_files()
	{
		$project_id = $this->input->post('project_id');

		$project = $this->project_model->get($project_id);
		$project->full_title = create_full_title($project);

		$this->load->model('section_model');
		$sections = $this->section_model->get_many_by('project_id', $project_id);

		if (empty($sections)) $this->ajax_output(array('message' => 'Sorry, there are no sections set up for this project. Please use the section compiler first.'), FALSE);;

		$copy_to_dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
		if (!is_dir($copy_to_dir)) $this->ajax_output(array('message' => 'Sorry, you do not seem to have a valid project directory.'), FALSE);

		foreach ($sections as $section)
		{
			if (empty($section->listen_url)) continue;

			//break up url to see if it is a local file location
			$local_file = $this->_local_file($section->listen_url);

			if ($local_file)
			{
				$file_name = $this->_copy_file($local_file, $copy_to_dir);
			}

			if (!$local_file or !$file_name) continue;

			$file_name = trim($file_name);
            $file_path = $copy_to_dir . '/' . $file_name;
            chmod($file_path, 0664);

            $file_data = $this->librivox_id3tag->_get_file_tags($file_path);
            if (isset($file_data['error'])) {
                unlink($file_path);
                $this->ajax_output(array('message' => 'File ' . $file_name . ' is not a valid MP3 file.'), FALSE);
            }

			$section->author = $this->_get_author_by_section_or_project($section);

			$tag_data = $this->librivox_id3tag->_build_tags($project, $section);

			$this->librivox_id3tag->_write_tag($copy_to_dir . $file_name, $tag_data);

			//let us write some information back to the section
			$fullid3_tags = $this->librivox_id3tag->_get_file_tags($copy_to_dir . $file_name);
			$playtime = round($fullid3_tags['playtime_seconds']);

			$this->librivox_id3tag->_update_section($section->id, array('file_name' => $file_name, 'playtime' => $playtime));
		}

		$this->ajax_output(array('message' => 'Files copied.', 'tags' => $tag_data, 'full_tags' => $fullid3_tags), TRUE);
	}

	function get_file_tags()
	{
		$project_id = $this->input->post('project_id');
		$file_name = $this->input->post('file_name');

		$project = $this->project_model->get($project_id);
		$localfile = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/' . $file_name;

		$fullid3_tags = $this->librivox_id3tag->_get_file_tags($localfile);

		//we don't need everything
		$section = $fullid3_tags['comments'];

		$this->ajax_output(array('message' => 'Section found', 'section' => $section), TRUE);
	}

	//when we upload a new file (vs. pulling from section compiler) this lets us link it to a specific section
	// for the project
	function link_section_data()
	{
		$project_id = $this->input->post('project_id', true);
		$file_name = $this->input->post('file_name', true);
		$file_name = trim($file_name);

		$section_number = $this->input->post('section_number', true);

		$this->load->model('section_model');
		$section = $this->section_model->get_by_section_number($project_id, $section_number);

		if (empty($section)) $this->ajax_output(array('message' => 'That section number was not found for this project.'), FALSE);

		$section->author = $this->_get_author_by_section_or_project($section);

		$project = $this->project_model->get($project_id);
		$project->full_title = create_full_title($project);

		$tag_data = $this->librivox_id3tag->_build_tags($project, $section);

		$copy_to_dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
		$this->librivox_id3tag->_write_tag($copy_to_dir . $file_name, $tag_data);

		//let us write some information back to the section
		$fullid3_tags = $this->librivox_id3tag->_get_file_tags($copy_to_dir . $file_name);
		$playtime = round($fullid3_tags['playtime_seconds']);

		$this->librivox_id3tag->_update_section($section->id, array('file_name' => $file_name, 'playtime' => $playtime));

		$this->ajax_output(array('message' => 'Section tags added'), TRUE);
	}

	// Update a single file's meta tags
	function update_metadata()
	{
		$post = $this->input->post(null, true);

		foreach ($post as $key => $value)
		{
			$post[$key] = trim($value);
		}

		$project_id = $post['project_id'];
		$project = $this->project_model->get($project_id);
		$file_path = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';

		$file_name = $post['file_name'];
		$original_file = $post['original_file'];

		unset($post['project_id']);
		unset($post['file_name']);
		unset($post['original_file']);

		$file_array = $this->librivox_id3tag->_get_file_tags($file_path . $original_file);

		if (!isset($file_array['comments']['track_number'])) $this->ajax_output(array('message' => 'All files must be linked to sections before you can edit the tags'), FALSE);

		$tag_data = $this->librivox_id3tag->_update_tags($file_array, $post);

		$this->librivox_id3tag->_write_tag($file_path . $original_file, $tag_data);

		rename($file_path . $original_file, $file_path . $file_name);

		$this->librivox_id3tag->_update_section_by_filename($original_file, array('file_name' => $file_name));

		$this->ajax_output(array('message' => 'Updated'), TRUE);
	}

	function adjust_file_volume($project_id = 0)
	{
		$project = $this->project_model->get($project_id);

		if (empty($project)) redirect(base_url('manage_dashboard'));

		//get directory path & contents
		$dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
		$map = $this->_get_dir_contents($dir);

		if (!empty($map))
		{
			$this->load->library('librivox_mp3gain');
			$this->librivox_mp3gain->adjust($dir, $map);
		}

		redirect(base_url('validator/' . $project_id));
	}

	// updates all files, based on action type
	function update_files()
	{
		$post = $this->input->post(null, true);

		foreach ($post as $key => $value)
		{
			$post[$key] = trim($value);
		}

		if (empty($post['project_id'])) $this->ajax_output(array('message' => 'Unspecified project'), FALSE);
		if (empty($post['action'])) $this->ajax_output(array('message' => 'Unspecified action'), FALSE);

		//just makes it all cleaner to work with later
		$project_id = $post['project_id'];
		$action = $post['action'];

		unset($post['project_id']);
		unset($post['action']);

		$project = $this->project_model->get($project_id);

		$post['num_sections'] = $project->num_sections;

		//get directory path & contents
		$dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
		$map = $this->_get_dir_contents($dir);

		if (!empty($map))
		{
			$i = 0;
			foreach ($map as $file)
			{
				if (empty($file)) continue;

				$i++;

				$file_name = $dir . $file;
				$file_array = $this->librivox_id3tag->_get_file_tags($file_name);

				if (!isset($file_array['comments']['track_number'])) $this->ajax_output(array('message' => 'All files must be linked to sections before you can edit the tags'), FALSE);

				if (empty($file_array['comments']['title'])) continue;

				if ($action == 'update_name')
				{
					// can be update_name,
					if (empty($file_array['comments']['track_number'][0])) continue;
					$this->librivox_id3tag->_update_name($file_name, $post, $project->has_preface, $file_array['comments']['track_number'][0]);
				}
				elseif ($action == 'reset_tracks')
				{
					$chapter_array = explode('-', $file_array['comments']['title'][0]);
					$track = (int)$chapter_array[0] + $project->has_preface;

					if (empty($track) || !is_numeric($track)) $track = $i;

					$tag_data = $this->librivox_id3tag->_update_tracks($file_array, $track);
					$this->librivox_id3tag->_write_tag($file_name, $tag_data);
				}
				else
				{
					// or update_album, update_artist
					$tag_data = $this->librivox_id3tag->_update_tags($file_array, $post);
					//var_dump($tag_data);
					$this->librivox_id3tag->_write_tag($file_name, $tag_data);
				}
			}
		}

		$this->ajax_output(array('message' => 'Updated'), TRUE);
	}

	function _local_file($listen_url)
	{
		//clean & check if is file
		if (strpos($listen_url, base_url()) === FALSE) return false;

		$local_path = str_replace(base_url(), '', $listen_url);
		if (!is_file($local_path)) return false;

		return $local_path;
	}

	function _copy_file($local_file, $copy_to_dir)
	{
		$file_array = explode('/', $local_file);
		$file_name = trim(end($file_array));
		$copied = copy($local_file, $copy_to_dir . '/' . $file_name);
		return ($copied) ? $file_name : false;
	}

	function _get_file_name_from_url($url)
	{
		$query_marker = strpos($url, '?');
		$url = ($query_marker) ? substr($url, 0, $query_marker) : $url;

		$file_array = explode('/', $url);
		$file_name = end($file_array);

		return $file_name;
	}

	function _get_dir_contents($dir)
	{
		$this->load->helper('directory');
		if (!is_dir($dir)) return false;
		return directory_map($dir);
	}

	function _get_author_by_section_or_project($section)
	{
		$author = $this->_get_author($section->author_id);
		if (!empty($author) && $author != '') return $author;

		$this->load->model('project_author_model');
		$project_author = $this->project_author_model->order_by('author_id', 'asc')->limit(1)->get_by(array('project_id' => $section->project_id, 'type' => 'author'));
		if (empty($project_author)) return '';

		$author = $this->_get_author($project_author->author_id);
		return $author;
	}


	// We use this to set default values in the "set id3tags" form. We just take the first author if more than one, hoping first
	//one entered was the main author. MC can completely overwrite this if needed
	function _get_author_by_project($project_id, $part = false)
	{
		$this->load->model('project_author_model');
		$project_author = $this->project_author_model->order_by('author_id', 'asc')->limit(1)->get_by(array('project_id' => $project_id, 'type' => 'author'));
		if (empty($project_author)) return '';

		$author = $this->_get_author($project_author->author_id, $part);

		return $author;
	}

	function _get_author($author_id, $part = false)
	{
		if (!$author_id) return '';

		$this->load->model('author_model');
		$author = $this->author_model->get($author_id);
		if (!$author) return '';

		if ($part == 'first') return trim(is_empty($author->first_name));

		if ($part == 'last') return trim(is_empty($author->last_name));

		return trim(is_empty($author->first_name) . ' ' . is_empty($author->last_name));
	}

	function delete_file()
	{
		//if they are in here, they are only an Admin or MC - no extra permissions checks required
		$post = $this->input->post(null, true);

		foreach ($post as $key => $value)
		{
			$post[$key] = trim($value);
		}

		if (empty($post['project_id'])) $this->ajax_output(array('message' => 'Unspecified project'), FALSE);
		if (empty($post['file_name'])) $this->ajax_output(array('message' => 'Unspecified file name'), FALSE);

		//just makes it all cleaner to work with later
		$project_id = $post['project_id'];
		$file_name = $post['file_name'];

		unset($post['project_id']);
		unset($post['file_name']);

		$project = $this->project_model->get($project_id);

		$this->load->model('section_model');
		$this->section_model->update_by(array('file_name' => $file_name, 'project_id' => $project_id), array('file_name' => null, 'playtime' => null));

		//get directory path & contents
		$full_file_path = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/' . $file_name;
		if (is_file($full_file_path))
		{
			unlink($full_file_path);
			$this->ajax_output(array('message' => 'File deleted'), TRUE);
		}

		$this->ajax_output(array('message' => 'Unable to find specified file'), FALSE);
	}

	// Validation tests

	function run_tests()
	{
		$post = $this->input->post(null, true);

		if (empty($post['project_id'])) $this->ajax_output(array('message' => 'Unspecified project'), FALSE);

		$project = $this->project_model->get($post['project_id']);

		$map = array();
		if (!empty($project->validator_dir))
		{
			$dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
			$map = $this->_get_dir_contents($dir);
		}

		$results = $this->librivox_filecheck->load_project_map($dir, $map, $project);

		$formatted_results = $this->format_test_results($results);

		$this->ajax_output(array('message' => 'Test results', 'results' => $results, 'formatted_results' => $formatted_results), TRUE);
	}

	function format_test_results($results)
	{
		//flip array(0=>'test_name') so we have test_name indexes
		$test_summary = array_flip($this->librivox_filecheck->test_array);

		//seed with an empty array so we can array_push
		foreach ($test_summary as $key => $value)
		{
			$test_summary[$key] = array();
		}

		foreach ($results['file_array'] as $key => $file)
		{
			foreach ($file['tests'] as $test_name => $test_result)
			{
				if (!$test_result)
				{
					array_push($test_summary[$test_name], $file['file_name']);
				}
			}
		}

		//add the errors from completeness test
		$test_summary['completeness'] = $results['errors'];

		return $test_summary;
	}

	//---- TESTING  -----//

	function test_index($project_id = 52)
	{
		echo 'Testing...';

		$project = $this->project_model->get($project_id);

		//var_dump($project);

		$map = array();
		if (!empty($project->validator_dir))
		{
			$dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
			$map = $this->_get_dir_contents($dir);
		}

		sort($map);

		//var_dump($map);

		$results = $this->librivox_filecheck->load_project_map($dir, $map, $project);

		//var_dump($results);

		//$this->format_test_results($results);

		//$results = $this->librivox_filecheck->check_file_completeness();

		var_dump($results);
	}

	function test_mp3gain($project_id = 52)
	{
		$this->data['project'] = $this->project_model->get($project_id);

		$map = array();
		if (!empty($this->data['project']->validator_dir))
		{
			$dir = './' . DIR_VALIDATOR . '/' . $this->data['project']->validator_dir . '/';

			$map = $this->_get_dir_contents($dir);
		}

		$results = $this->librivox_mp3gain->analyze($dir, $map);
		// $results[2] is the different from default (89)

	}

	function test_get_author()
	{
		$section = new stdClass();
		$section->project_id = 6781;
		$section->author_id = null;
		echo $this->_get_author_by_section_or_project($section);
	}

	function test_sorting()
	{
		$data[] = array('volume' => 67, 'edition' => 2);
		$data[] = array('volume' => 86, 'edition' => 1);
		$data[] = array('volume' => 85, 'edition' => 6);
		$data[] = array('volume' => 98, 'edition' => 2);
		$data[] = array('volume' => 86, 'edition' => 6);
		$data[] = array('volume' => 67, 'edition' => 7);

		foreach ($data as $key => $row)
		{
			$volume[$key] = $row['volume'];
			//$edition[$key] = $row['edition'];
		}

		// Sort the data with volume descending, edition ascending
		// Add $data as the last parameter, to sort by the common key
		array_multisort($volume, SORT_DESC, $data);

		var_dump($data);
	}

	function test_write_tag()
	{
		$file = './' . DIR_VALIDATOR . '/tales_secret_egypt_1212/' . '/The_Yashmak_of_Pearls.mp3';

		// populate data array
		$TagData = array(
			//'title'   => array('My Song'),
			'artist' => array('The Best Artist'),
			'album' => array('the Greatest Hits'),
			//'year'    => array('2004'),
			//'genre'   => array('Rock'),
			//'comment' => array('excellent!'),
			//'track'   => array('04/16'),
		);

		$this->write_tag($file, $TagData);
	}

	function test_update_tags()
	{
		$file_array = array(
			'comments' => array(
				'title' => array('Chapter 1'),  //title is for chapter
				'artist' => array('Jeff Madsen'),
				'album' => array('Blah'),
				'track_number' => array(1),
			)
		);

		$post = array('album' => 'Abbas Greatest Hits');
		$merged = $this->_update_tags($file_array, $post);

		var_dump($merged);

		$file = './' . DIR_VALIDATOR . '/all_new_jeff_test_1301/' . '/letters_of_two_brides_01_balzac.mp3';

		$this->_write_tag($file, $merged);
	}

	function test_get_tags()
	{
		$file_name = './' . DIR_VALIDATOR . '/30_1301/' . '/Breath_of_Allah.mp3';
		$file_array = $this->_get_file_tags($file_name);
		var_dump($file_array);
		echo $file_array['playtime_string'];
	}

	function test_local_file()
	{
		$listen_url = 'https://librivox.local/uploads/tests/Omar of Ispahân.mp3';

		$exists = $this->_local_file($listen_url);
		echo $exists;
	}

	function test_copy_file()
	{
		$file = 'uploads/cs/The_Yashmak_of_Pearls.mp3';
		$copy_to_dir = './' . DIR_VALIDATOR . '/tales_secret_egypt_1212/';
		$copied = $this->_copy_file($file, $copy_to_dir);

		echo $copied;
	}

	function test_get_file()
	{
		//echo 'TEST';

		//$file_url = 'https://librivox.greenkri.com/uploader/incoming/count_of_monte_cristo_003_dumas.mp3';
		//$copy_to_dir    = './' . DIR_VALIDATOR . '/count_of_monte_cristo_1302/';

		//$this->_get_file($file_url, $copy_to_dir);

		//echo $this->_get_file_name_from_url($file_url); return;

		$project_id = 47;

		$project = $this->project_model->get($project_id);
		$project->full_title = create_full_title($project);

		$this->load->model('section_model');
		$sections = $this->section_model->get_many_by('project_id', $project_id);

		//if(empty($sections)) $this->ajax_output(array('message'=>'Sorry, there are no sections set up for this project. Please use the section compiler first.') , FALSE);;

		$copy_to_dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
		//if(!is_dir($copy_to_dir)) $this->ajax_output(array('message'=>'Sorry, you do not seem to have a valid project directory.') , FALSE);

		$section = $sections[1];

		if (empty($section->listen_url)) echo 'No url';

		//var_dump($section); return;

		//break up url to see if it is a local file location
		$local_file = $this->_local_file($section->listen_url);

		if ($local_file)
		{
			$file_name = $this->_copy_file($local_file, $copy_to_dir);
		}

		if (!$local_file or !$file_name)
		{
			echo 'No file name';
			return;
		}

		$section->author = $this->_get_author_by_section_or_project($section);

		$tag_data = $this->librivox_id3tag->_build_tags($project, $section);

		$this->librivox_id3tag->_write_tag($copy_to_dir . $file_name, $tag_data);

		//let us write some information back to the section
		$fullid3_tags = $this->librivox_id3tag->_get_file_tags($copy_to_dir . $file_name);
		$playtime = round($fullid3_tags['playtime_seconds']);

		$this->librivox_id3tag->_update_section($section->id, array('file_name' => $file_name, 'playtime' => $playtime));
	}

}

/* End of file validator.php */
/* Location: ./application/controllers/validator.php */
