<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statistics extends CI_Controller {


	public function __construct()
	{
		//if (!$this->input->is_cli_request()) return false;

		parent::__construct();

		$this->load->model('stats_model');

		// "php ../public_html/index.php cron delete_frozen_files index"

	}

  //updates stats
  public function stats()
  {
      $array['total_projects']       = $this->stats_model->project_count_completed();
      $array['projects_last_month']  = $this->stats_model->monthly_stats(1, 1)->project_count;
      $array['non_english_projects'] = $this->stats_model->project_count_completed_nonenglish();
      $array['number_languages']     = $this->stats_model->language_count_with_completed();
      $array['number_readers']       = $this->stats_model->reader_count();
      $array['updated']              = time();

      $this->load->model('statistics_model');
      $this->statistics_model->insert($array);
  }


}