<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_table_update extends CI_Controller {


	public function __construct()
	{
		//if (!$this->input->is_cli_request()) return false;
		parent::__construct();

		// "php ../public_html/index.php cron Search_table_update search_table"
	}

	public function search_table()
	{

		$sql = "TRUNCATE TABLE search_table;"; //look into a way to only add new 
		$this->db->query($sql);

		$sql = 'INSERT INTO search_table (search_field, source_table, source_id)
				SELECT CONCAT(p.title_prefix, " " ,p.title), "projects", p.id
				FROM projects p 
				WHERE p.id NOT IN (SELECT project_id FROM group_projects WHERE project_id IS NOT NULL);';
		$this->db->query($sql);


		$sql = 'INSERT INTO search_table (search_field, source_table, source_id)
				SELECT gr.name, "groups", gr.id
				FROM groups gr;	';
		$this->db->query($sql);

		$sql = 'INSERT INTO search_table (search_field, source_table, source_id)
				SELECT CONCAT(p.title_prefix, " " ,p.title), "groups", gr.id
				FROM groups gr
				JOIN group_projects gp ON (gp.group_id = gr.id)	
				JOIN projects p ON (p.id = gp.project_id);';
		$this->db->query($sql);
	
		/*	
		$sql = 'INSERT INTO search_table (search_field, source_table, source_id, section_language_id, section_author_id)
				SELECT s.title, "sections", p.id, s.language_id, s.author_id
				FROM projects p
				JOIN sections s ON (p.id = s.project_id)
				WHERE p.is_compilation = 1
				AND p.status = "Complete";';		
		$this->db->query($sql);
		*/

		$sql = 'INSERT INTO search_table (search_field, source_table, source_id, section_language_id, section_author_id, section_reader_id)
				SELECT s.title, "sections", p.id, s.language_id, s.author_id, sr.reader_id
				FROM projects p
				JOIN sections s ON (p.id = s.project_id)
				JOIN section_readers sr ON (sr.section_id = s.id)
				WHERE p.is_compilation = 1
				AND p.status = "Complete";';		
		$this->db->query($sql);		

		echo 'Finished';

	}

}	