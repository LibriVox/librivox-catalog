<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project_keyword_model extends MY_Model {

   public function set_keywords_statistics_by_project($project_id)
    {        
        $sql = '
 	UPDATE keywords k
	JOIN
		(SELECT pk.keyword_id as keyword_id, COUNT(pk.project_id) AS count
        	FROM project_keywords pk
		GROUP BY 1
		) as sub
	ON k.id = sub.keyword_id
	SET k.count = sub.count 
	WHERE k.id IN
		(SELECT keyword_id 
        	FROM project_keywords
        	WHERE project_id = ?)';


        $query = $this->db->query($sql, array($project_id));
    }


}

/* End of file project_keyword.php */
/* Location: ./application/models/project_keyword.php */
