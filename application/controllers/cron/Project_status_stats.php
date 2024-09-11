<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project_status_stats extends CI_Controller
{

	private $status_complete;

	private $status_in_progress;

	public function __construct()
	{
		//if (!$this->input->is_cli_request()) return false;

		parent::__construct();

		$this->status_complete = array(PROJECT_STATUS_COMPLETE);

		$this->status_in_progress = array(PROJECT_STATUS_OPEN, PROJECT_STATUS_FULLY_SUBSCRIBED, PROJECT_STATUS_PROOF_LISTENING, PROJECT_STATUS_VALIDATION);
		// "php ../public_html/index.php cron project_status_stats author"

	}

	public function author()
	{
		// Reset completion and in-progress counts for all authors
		// This avoids an edge-case in the later queries: if there are no projects with the given author or status, they leave meta_complete
		//  and meta_in_progress untouched, resulting in 'phantom' project counts.
		$this->db->query('
			UPDATE authors
			SET meta_complete = 0,
				meta_in_progress = 0');

		$sql = '
			UPDATE authors
			JOIN 
				(SELECT a.id,  COUNT(*) AS count
				FROM authors a 
				JOIN project_authors pa ON (a.id = pa.author_id) 
				JOIN projects p ON (p.id = pa.project_id)
				WHERE p.status IN ' . sprintf('("%s")', implode('","', $this->status_complete)) . '
				GROUP BY a.id ) as metadata ON (authors.id = metadata.id)
			SET meta_complete = metadata.count ';
		$this->db->query($sql);

		$sql = '
			UPDATE authors
			JOIN 
				(SELECT a.id,  COUNT(*) AS count
				FROM authors a 
				JOIN project_authors pa ON (a.id = pa.author_id) 
				JOIN projects p ON (p.id = pa.project_id)
				WHERE p.status IN ' . sprintf('("%s")', implode('","', $this->status_in_progress)) . '
				GROUP BY a.id ) as metadata ON (authors.id = metadata.id)
			SET meta_in_progress = metadata.count ';
		$this->db->query($sql);
	}

	public function genre()
	{
		$sql = '
			UPDATE genres
			JOIN 
				(SELECT g.id,  COUNT(*) AS count
				FROM genres g 
				JOIN project_genres pg ON (g.id = pg.genre_id) 
				JOIN projects p ON (p.id = pg.project_id)
				WHERE p.status IN ' . sprintf('("%s")', implode('","', $this->status_complete)) . '
				GROUP BY g.id ) as metadata ON (genres.id = metadata.id)
			SET meta_complete = metadata.count ';
		$this->db->query($sql);

		$sql = '
			UPDATE genres
			JOIN 
				(SELECT g.id,  COUNT(*) AS count
				FROM genres g 
				JOIN project_genres pg ON (g.id = pg.genre_id) 
				JOIN projects p ON (p.id = pg.project_id)
				WHERE p.status IN ' . sprintf('("%s")', implode('","', $this->status_in_progress)) . '
				GROUP BY g.id ) as metadata ON (genres.id = metadata.id)
			SET meta_in_progress = metadata.count ';
		$this->db->query($sql);
	}

	public function language()
	{
		$sql = '
			UPDATE languages
			SET meta_complete = 0 ';
		$this->db->query($sql);

		$sql = '
			UPDATE languages
			JOIN 
				(SELECT l.id,  COUNT(*) AS count
				FROM languages l 
				JOIN projects p ON (p.language_id = l.id)
				WHERE p.status IN ' . sprintf('("%s")', implode('","', $this->status_complete)) . '
				GROUP BY l.id ) as metadata ON (languages.id = metadata.id)
			SET meta_complete = metadata.count ';
		$this->db->query($sql);

		$sql = '
			UPDATE languages
			JOIN 
			        (SELECT s.language_id, COUNT(*) AS count
			        FROM sections s 
			        JOIN projects p ON (p.id = s.project_id)
			        WHERE p.status IN ' . sprintf('("%s")', implode('","', $this->status_complete)) . '
			        AND p.is_compilation = 1 
			        GROUP BY s.language_id ) as metadata ON (languages.id = metadata.language_id)
			SET meta_complete = meta_complete + metadata.count';
		$this->db->query($sql);

		$sql = '
			UPDATE languages
			JOIN 
				(SELECT l.id,  COUNT(*) AS count
				FROM languages l 
				JOIN projects p ON (p.language_id = l.id)
				WHERE p.status IN ' . sprintf('("%s")', implode('","', $this->status_in_progress)) . '
				GROUP BY l.id ) as metadata ON (languages.id = metadata.id)
			SET meta_in_progress = metadata.count ';
		$this->db->query($sql);
	}
}


/*
ALTER TABLE `authors` ADD `meta_complete` INT( 4 ) NOT NULL DEFAULT '0' AFTER `blurb` ,
ADD `meta_in_progress` INT( 4 ) NOT NULL DEFAULT '0' AFTER `meta_complete` 

ALTER TABLE `genres` ADD `meta_complete` INT( 4 ) NOT NULL DEFAULT '0' AFTER `archive` ,
ADD `meta_in_progress` INT( 4 ) NOT NULL DEFAULT '0' AFTER `meta_complete` 

ALTER TABLE `languages` ADD `meta_complete` INT( 4 ) NOT NULL DEFAULT '0' AFTER `three_letter_code` ,
ADD `meta_in_progress` INT( 4 ) NOT NULL DEFAULT '0' AFTER `meta_complete` 

*/

/* End of file project_status_stats.php */
/* Location: ./application/controllers/project_status_stats.php */
