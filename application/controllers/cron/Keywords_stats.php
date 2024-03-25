<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Keywords_stats extends CI_Controller
{

	public function update_keywords_stats()
	{
		$sql = '
			UPDATE keywords
			JOIN
			(SELECT pk.keyword_id as keyword_id, COUNT(pk.project_id) AS count
        		FROM project_keywords pk
        		GROUP BY 1
			) as sub
			ON keywords.id = sub.keyword_id
			SET keywords.instances = sub.count ';
		$this->db->query($sql);

	}



}


/*
ALTER TABLE `keywords` ADD `instances` INT( 4 ) NOT NULL DEFAULT '1' AFTER `value`; 

*/

/* End of file Keyword_stats.php */
/* Location: ./application/controllers/cron/Keyword_stats.php */
