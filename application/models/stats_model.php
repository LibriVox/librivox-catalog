<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stats_model extends CI_Model {

	function mc_stats()
	{
		$active_count = ' SUM( CASE 
			WHEN p.status = "open" THEN 1 
			WHEN p.status = "fully_subscribed" THEN 1
			WHEN p.status = "proof_listening" THEN 1
			WHEN p.status = "validation" THEN 1
			ELSE 0 END) ';

		$sql = 'SELECT u.username, '.$active_count.' AS active_count, u.max_projects
		FROM users u 
		JOIN projects p ON (u.id = p.person_mc_id ) 
		WHERE p.status IN ("open", "fully_subscribed", "proof_listening", "validation")
		GROUP BY u.username , u.max_projects';

		$query = $this->db->query($sql);

		return $query->result();

	}

	function monthly_stats($offset =0, $limit =0)
	{
		$sql = 'SELECT DATE_FORMAT(date_catalog,"%m-%Y") AS date_catalog, COUNT(*) AS project_count
			FROM projects p
			WHERE date_catalog IS NOT NULL
			AND DATE_FORMAT(date_catalog,"%m-%Y") != "00-0000"
			GROUP BY DATE_FORMAT(date_catalog,"%m-%Y") 
			ORDER BY DATE_FORMAT(date_catalog,"%Y-%m") DESC';

		if ($limit)
		{
			$sql .= ' LIMIT ' . $offset . ', '. $limit;  
		}	

		$query = $this->db->query($sql);

		if ($limit ==1)
		{
			return $query->row();  
		}		

		return $query->result();
	}

	function yearly_stats()
	{
		$sql = 'SELECT DATE_FORMAT(date_catalog,"%Y") AS date_catalog, COUNT(*) AS project_count
			FROM projects p
			WHERE date_catalog IS NOT NULL
			AND DATE_FORMAT(date_catalog,"%Y") != "0000"
			GROUP BY DATE_FORMAT(date_catalog,"%Y") 
			ORDER BY DATE_FORMAT(date_catalog,"%Y") DESC';

		$query = $this->db->query($sql);

		return $query->result();
	}

	// stats page - some of these are simple wrappy that allow us to customize later
	function project_count()
	{
		$sql = 'SELECT COUNT(*) AS project_count
			FROM projects p ';

		$query = $this->db->query($sql);

		return $query->row()->project_count;
	}

	function project_count_completed()
	{
		$sql = 'SELECT COUNT(*) AS project_count
			FROM projects p 
			WHERE status IN ("complete") ';

		$query = $this->db->query($sql);

		return $query->row()->project_count;		
	}

	function project_count_completed_nonenglish()
	{
		$sql = 'SELECT COUNT(*) AS project_count
			FROM projects p 
			WHERE status IN ("complete") 
			AND language_id != 1';

		$query = $this->db->query($sql);

		return $query->row()->project_count;			
	}

	function language_count()
	{
		$sql = 'SELECT COUNT(*) AS language_count
			FROM languages l ';

		$query = $this->db->query($sql);

		return $query->row()->language_count;		
	}

	function language_count_with_completed()
	{
		$sql = 'SELECT COUNT(DISTINCT language_id) AS language_count
			FROM projects p 
			WHERE status IN ("complete") 
			';

		$query = $this->db->query($sql);

		return $query->row()->language_count;		
	}

	function project_count_completed_solo()
	{
		$sql = 'SELECT COUNT(*) AS project_count
			FROM projects p 
			WHERE status IN ("complete") 
			AND project_type ="solo"
			';

		$query = $this->db->query($sql);

		return $query->row()->project_count;		
	}

	function reader_count()
	{
		$sql = 'SELECT COUNT(DISTINCT sr.reader_id) AS reader_count
			FROM section_readers sr 
			';

		$query = $this->db->query($sql);

		return $query->row()->reader_count;		
	}

	function reader_count_with_completed()
	{
		
	}		


	function active_projects()
	{
		   // project name, url_forum (status != complete), section count, no. sections assigned /pct, sections completed %, project status, PL?, MC name

		$sql = ' 
			SELECT p.id, p.status, CONCAT(IFNULL(p.title_prefix, ""), " ",p.title) AS title, p.person_mc_id, p.person_pl_id, p.num_sections, p.url_forum
			FROM projects p
			WHERE p.status != ?;
		';

		$query = $this->db->query($sql, array(PROJECT_STATUS_COMPLETE));

		return $query->result();

	} 					
}

/* End of file stats_model.php */
/* Location: ./application/models/stats_model.php */
