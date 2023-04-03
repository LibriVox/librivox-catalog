<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Iarchive_upload extends Private_Controller
{

	public function upload()
	{
		set_time_limit(0);

		$fields = $this->input->post(null, true);

		$this->load->model('project_model');
		$project = $this->project_model->get($fields['project_id']);

		if (empty($project)) $this->ajax_output(array('message' => 'Unable to locate project with that id'), false);

		if (empty($project->validator_dir)) $this->ajax_output(array('message' => 'Unable to locate project with that id'), false);

		$dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
		$files = $this->_get_dir_contents($dir);

		if (empty($files)) $this->ajax_output(array('message' => 'There are no files in this directory'), false);

		//$this->ajax_output(array('files'=>$files), true);

		//load config outside of the library so library can be framework agnostic
		$this->load->config('iarchive_uploader');
		$config = $this->config->item('iarchive_uploader');
		$this->load->library('Iarchive_uploader', $config);

		$params = array();

		//I think we need to load these files one by one; I will investigate doing a single upload,
		//but am worried about several technical issues

		$params['project_slug'] = $fields['upload_name'];
		$params['title'] = $fields['upload_title'];
		$params['file_location'] = $dir;

		//additional params
		$this->load->model('author_model');
		$this->load->model('keyword_model');

		$params['creator'] = $this->author_model->create_author_list($project->id, 'author');
		$params['date'] = date('Y-m-d');
		$params['subject'] = 'librivox; audiobooks;' . $this->keyword_model->create_keyword_list($project->id);
		$params['licenseurl'] = LICENSE_LINK;

		$description = $this->_get_full_description($params, $project);
		$params['description'] = trim(preg_replace('/\s+/', ' ', $description));  //trims all newlines before placing in header
		$params['language'] = $this->data['language'];

		// Close db connection before uploading to avoid hogging connections
		$this->db->close();

		foreach ($files as $key => $filename)
		{
			$params['filename'] = $filename;
			$retval = $this->iarchive_uploader->curl($params);
			//$retval ='test';

			//var_dump($retval);
			if ($retval == 'File error: File not found') $this->ajax_output(array('message' => 'Unable to locate file for upload'), false);
		}

		// Re-open db connection to write info we got from IA
		$this->load->database();

		$link = '<a href="' . $config['iarchive_project_page'] . '/' . $params['project_slug'] . '">Internet Archive</a>';

		//update the project
		$update['url_iarchive'] = $config['iarchive_project_page'] . '/' . $params['project_slug'];
		$update['zip_url'] = 'https://www.archive.org/download/' . $params['project_slug'] . '/' . $params['project_slug'] . '_64kb_mp3.zip';

		$this->project_model->update($project->id, $update);

		//https://archive.org/download/grainofdust_test_1307_librivox/grainofdust_test_1307_librivox_64kb_mp3.zip
		//http://www.archive.org/download/secret_garden_librivox/secret_garden_librivox_64kb_mp3.zip

		//update project sections' file urls
		$this->_update_section_urls($project, $params);

		$this->_update_section_sizes($project);

		$this->_update_total_runtime($project);

		$this->_update_total_zipsize($project);

		$this->ajax_output(array('message' => 'Success', 'link' => $link, 'files' => $files, 'params' => $params, 'retval' => $retval), true);
	}

	function _get_dir_contents($dir)
	{
		$this->load->helper('directory');
		if (!is_dir($dir)) return false;
		return directory_map($dir);
	}

	function _get_full_description($params, $project)
	{
		$this->data['title'] = empty($project->title_prefix) ? $project->title : $project->title_prefix . ' ' . $project->title;
		$this->data['authors'] = $params['creator'];

		$this->load->model('author_model');
		$translators = $this->author_model->create_author_list($project->id, 'translator');
		$this->data['translators'] = (empty($translators)) ? '' : '(Translated by ' . $translators . '.)';

		$this->load->model('language_model');
		$language = $this->language_model->get($project->language_id);

		$this->data['language'] = empty($language) ? 'English' : $language->language;

		$this->load->model('project_model');
		$this->data['readers'] = $this->project_model->create_project_reader_list($project->id);

		$this->data['description'] = $project->description;
		$this->data['catalog_url'] = $project->url_librivox;

		return $this->load->view('private/validator/archive_description', $this->data, TRUE);
	}

	function _update_section_urls($project, $params)
	{
		// http://www.archive.org/download/archiveuploadname/filename_64kb.mp3
		// http://www.archive.org/download/archiveuploadname/filename_128kb.mp3

		$iarchive_url = 'https://www.archive.org/download/' . $params['project_slug'] . '/';

		$this->load->model('section_model');
		return $this->section_model->update_iarchive_urls($project->id, $iarchive_url);
	}

	function _update_section_sizes($project)
	{
		$this->load->model('section_model');
		return $this->section_model->update_section_sizes($project->id);
	}

	function _update_total_runtime($project)
	{
		$this->load->helper('previewer');
		$this->load->model('section_model');
		$runtime = $this->section_model->get_total_project_runtime($project->id);

		if (empty($runtime)) $runtime = 0;

		$totaltime = format_playtime($runtime);

		$this->load->model('project_model');
		$this->project_model->update($project->id, array('totaltime' => $totaltime));
	}

	function _update_total_zipsize($project)
	{
		$this->load->model('section_model');
		$zip_size = $this->section_model->get_total_zipsize($project->id);

		if (empty($zip_size)) $zip_size = 0;

		$this->load->model('project_model');
		$this->project_model->update($project->id, array('zip_size' => $zip_size . 'MB'));
	}

	//////////////////////////////////////////////////////////////////////////////

	function test_upload()
	{
		set_time_limit(0);
		echo 'Start:' . date('H:i:s');

		$i = 20;

		$_POST['project_id'] = 7388;
		$_POST['upload_name'] = 'librivox_testing_20130129_madsen_' . $i;
		$_POST['upload_title'] = 'LIBRIVOX TESTING: ' . $i;
		$this->upload();

		echo ' End:' . date('H:i:s');
		//echo "maximum execution time is ".ini_get('max_execution_time');
	}

	function test_files()
	{
		$project_id = 314;

		$this->load->model('project_model');
		$project = $this->project_model->get($project_id);

		$dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/';
		$files = $this->_get_dir_contents($dir);

		var_dump($files);

		$this->load->library('Librivox_id3tag');

		foreach ($files as $key => $file_name)
		{
			$localfile = './' . DIR_VALIDATOR . '/' . $project->validator_dir . '/' . $file_name;
			$fullid3_tags = $this->librivox_id3tag->_get_file_tags($localfile);

			var_dump($fullid3_tags);
		}
	}
}

/* End of file iarchive_upload.php */
/* Location: ./application/controllers/iarchive_upload.php */
