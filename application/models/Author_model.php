<?php

class Author_model extends MY_Model
{

	public function search_by($term, $search_field)
	{
		$query = $this->db->select('*')->where(array('linked_to'=>'0'))->order_by($search_field)->like($search_field, $term)->get($this->_table);
		return $query->result();
	}

	//uses comma-delimited list of author ids in a where_in
	public function get_author_list($auth_id_list)
	{
		$sql = '
		SELECT id, first_name, last_name, author_url, dob, dod, id AS author_id  
		FROM '. $this->_table . '
		WHERE '. $this->primary_key .'  IN (' .$auth_id_list. ')
		AND linked_to = 0 ';
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	public function get_author_list_by_project($project_id, $type='author')
	{
		$sql = '
		SELECT id, first_name, last_name, author_url, dob, dod, id AS author_id  
		FROM authors a 
		JOIN project_authors pa ON (a.id = pa.author_id)
		WHERE pa.project_id = ?
		AND pa.type = ?
		AND linked_to = 0 ';
		$query = $this->db->query($sql, array($project_id, $type));

		return $query->result_array();
	}


	public function get_author_suggestions($first_name, $last_name)
	{
		$sql = '
		SELECT id, first_name, last_name, author_url, dob, dod  
		FROM '. $this->_table . '
		WHERE SOUNDEX(CONCAT(first_name, " ",last_name)) = SOUNDEX("'. $first_name. ' ' .$last_name .'")
		AND linked_to = 0 ';
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	//return comma delimited list of authors from project
	public function create_author_list($project_id, $type = 'author')
	{
		$sql = 'SELECT CONCAT(a.first_name, " " , a.last_name) as author
		FROM authors a 
		JOIN project_authors pa ON (pa.author_id = a.id AND pa.type = ?)
		WHERE pa.project_id = ?
		AND linked_to = 0 ';

		$query = $this->db->query($sql, array($type, $project_id));

		$author_list = array();

		if ($query->num_rows())
		{
			foreach ($query->result() as $author)
			{
				$author_list[] = $author->author;
			}	
		}

		return implode('; ', $author_list);	
	}

	public function get_author_projects($author_id)
	{
		$sql = '
			SELECT p.id, TRIM(CONCAT(p.title_prefix, " ", p.title )) AS title
		 	FROM projects p 
		 	JOIN project_authors pa ON (pa.project_id = p.id) 
		 	WHERE pa.author_id = ? ';

		$query = $this->db->query($sql, array($author_id)); 
		
		return $query->result();	

	}	

	public function get_authors_with_pseudonyms($offset=0, $limit=CATALOG_RESULT_COUNT)
	{
		$sql = "SELECT id AS author_id, first_name, last_name, dob, dod, '' AS real_first_name, '' AS real_last_name, meta_complete, meta_in_progress
				FROM authors
				WHERE authors.linked_to = 0
				UNION
				SELECT ap.author_id, ap.first_name, ap.last_name, a.dob, a.dod, a.first_name, a.last_name, 0, 0
				FROM author_pseudonyms ap
				JOIN authors a ON (ap.author_id = a.id)
				WHERE a.linked_to = 0
				ORDER BY 3 ASC";

		if ($limit) $sql .= " LIMIT $offset, $limit";

		$query = $this->db->query($sql); 		
		return $query->result_array();	
	}


}