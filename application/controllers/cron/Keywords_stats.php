<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Keywords_stats extends CI_Controller
{
	public function update_keywords_stats()
	{
		// Some keywords are not in use at all.
		// Initialise count field to zero before tallying counts.
		
		$sql = '
			UPDATE keywords
			SET count = 0';
		$this->db->query($sql);
		
		$sql = '
			UPDATE keywords
			JOIN
			(SELECT pk.keyword_id as keyword_id, COUNT(pk.project_id) AS count
        		FROM project_keywords pk
        		GROUP BY 1
			) as sub
			ON keywords.id = sub.keyword_id
			SET keywords.count = sub.count ';
		$this->db->query($sql);

	}
}


/* End of file Keyword_stats.php */
/* Location: ./application/controllers/cron/Keyword_stats.php */

