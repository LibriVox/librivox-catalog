<?php

class Author_model extends MY_Model
{
	public function autocomplete($term, $search_field, $filter_term, $filter_field)
	{

		if (!preg_match("/[\w]/", $term))
			return array();

		$name_clause = '';
		$bindings = array();
		$sort_order = 'TRIM(COALESCE(pseudo_first, first_name)), TRIM(COALESCE(pseudo_last, last_name))';

		switch ($search_field)
		{
			default:
				return array();

			case 'first_name':
				$name_parts = preg_split('/ /', $term, 2, PREG_SPLIT_NO_EMPTY); // Split by first space

				// Search first word as first name
				$name_clause .= '
						AND (match_table.first_name LIKE ?
							OR (match_table.first_name = "" AND match_table.last_name LIKE ?) )';
				$bindings =  array_merge($bindings, array('%'. $name_parts[0] .'%', '%'. $name_parts[0] .'%'));

				if (!empty($name_parts[1]))
				{
					// If there's a second word, refine the search.  Match the entire term against the full name.
					$name_clause .= '
						AND CONCAT(match_table.first_name, " ", match_table.last_name) LIKE ?';
					$bindings =  array_merge($bindings, array('%'. $term .'%'));
				}
				elseif (empty($filter_term))
				{
					$sort_order = 'TRIM(COALESCE(pseudo_last, last_name)), TRIM(COALESCE(pseudo_first, first_name))';
				}
				break;

			case 'last_name':
				$name_parts = preg_split('/, ?/', $term, 2, PREG_SPLIT_NO_EMPTY); // Split by first comma

				// Search before the comma as last name
				$name_clause .= '
						AND match_table.last_name LIKE ?';
				$bindings =  array_merge($bindings, array('%'. $name_parts[0] .'%'));

				if (!empty($name_parts[1]))
				{
					// If there's anything following a comma, refine by searching that as the first name.
					$name_clause .= '
						AND (match_table.first_name LIKE ?
							OR (match_table.first_name = "" AND match_table.last_name LIKE ?) )';
					$bindings =  array_merge($bindings, array('%'. $name_parts[1] .'%', '%'. $name_parts[1] .'%'));
				}
				break;

			case 'full_name':
				$name_parts = preg_split('/, ?/', $term, 2, PREG_SPLIT_NO_EMPTY); // Split by first comma

				if (empty($name_parts[1]))
				{
					// No comma: assume we're searching anywhere within a full name (might be slower than other search modes, since nothing narrows our selection first)
					$name_clause .= '
						AND CONCAT(match_table.first_name, " ", match_table.last_name) LIKE ?';
					$bindings =  array_merge($bindings, array('%'. $name_parts[0] .'%'));
				}
				else
				{
					// With text following a comma: assume we're searching "Lastname, Firstname"
					$name_clause .= '
						AND match_table.last_name LIKE ?
						AND (match_table.first_name LIKE ?
							OR (match_table.first_name = "" AND match_table.last_name LIKE ?) )';
					$bindings =  array_merge($bindings, array('%'. $name_parts[0] .'%', '%'. $name_parts[1] .'%', '%'. $name_parts[1] .'%'));
				}
				break;
		}


		// An additional option, which we have on the Template Generator but not Section Compiler, is filtering the results of one box by what's in the other.
		switch ($filter_field)
		{
			case 'first_name':
				$name_clause .= '
					AND (match_table.first_name LIKE ?
						OR (match_table.first_name = "" AND match_table.last_name LIKE ?) )';
				$bindings =  array_merge($bindings, array('%'. $filter_term .'%', '%'. $filter_term .'%'));
				break;

			case 'last_name':
				$name_clause .= '
						AND match_table.last_name LIKE ?';
				$bindings =  array_merge($bindings, array('%'. $filter_term .'%'));
				break;
		}

		// Since we use the name-matching clause once for authors and once for pseudonyms, we need the bindings for it twice.
		$bindings = array_merge($bindings, $bindings);

		$sql = 'SELECT id, first_name, last_name, dob, dod, author_url, NULL AS pseudo_first, NULL AS pseudo_last
			FROM authors match_table
			WHERE linked_to = 0
			'. $name_clause .'
			UNION
			SELECT a.id, a.first_name, a.last_name, dob, dod, author_url, match_table.first_name AS pseudo_first, match_table.last_name AS pseudo_last
			FROM author_pseudonyms match_table
			JOIN authors a ON (a.id = match_table.author_id)
			WHERE linked_to = 0
			'. $name_clause .'
			ORDER BY '. $sort_order .'
			LIMIT '. AUTOCOMPLETE_LIMIT;

		$query = $this->db->query($sql, $bindings);
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