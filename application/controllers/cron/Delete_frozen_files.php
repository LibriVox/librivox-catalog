<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Delete_frozen_files extends CI_Controller
{

	public function __construct()
	{
		//if (!$this->input->is_cli_request()) return false;

		parent::__construct();
		// "php ../public_html/index.php cron delete_frozen_files index"
	}

	function index()
	{
		$this->load->model('project_model');

		$frozen_projects = $this->project_model->get_frozen_projects();

		if (!empty($frozen_projects))
		{
			foreach ($frozen_projects as $key => $project)
			{
				$dir = './' . DIR_VALIDATOR . '/' . $project->validator_dir;

				$cmd = 'rm -R ' . $dir;

				exec($cmd);
			}
		}
	}
}
