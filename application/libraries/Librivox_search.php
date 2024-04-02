<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Librivox_search{

	// this is searching logic for the main, public-facing catalog search
	// may be some complications, so keeping it all together here


	public function __construct()
	{

	}

	public function __get($var)
	{
		return get_instance()->$var;
	}


	/*
	*
	* Advanced title search. All conditions are AND
	*
	*
	*/
	function advanced_title_search($params)
	{

		$params['like_left'] = '%';
		$params['like_right'] = '%';

		$sql = '';

		/* Title*/
		$title = ' ';
		if (!empty($params['title']) )
		{
			$title_search = '%' . str_replace(' ', '%', $params['title']) . '%';
			$title = 'AND st.`search_field` LIKE ' . $this->db->escape($title_search);
		}

		/* Status */

		$status = ' ';
		if (!empty($params['status']) )
		{

			$status_group['complete']		= array(PROJECT_STATUS_COMPLETE);
			$status_group['in_progress']	= array(PROJECT_STATUS_FULLY_SUBSCRIBED, PROJECT_STATUS_PROOF_LISTENING, PROJECT_STATUS_VALIDATION);
			$status_group['open']			= array(PROJECT_STATUS_OPEN);
			$status_group['all']			= array(PROJECT_STATUS_COMPLETE, PROJECT_STATUS_OPEN, PROJECT_STATUS_FULLY_SUBSCRIBED, PROJECT_STATUS_PROOF_LISTENING, PROJECT_STATUS_VALIDATION);

			$status_array = sprintf('"%s"', implode('", "', $status_group[$params['status']]));
			$status = ' AND p.status IN ('. $status_array .') ';
		}

		/* Project type */

		$project_type = ' ';
		if (!empty($params['project_type']) && $params['project_type'] != 'either')
		{
			if ($params['project_type'] == 'solo')
			{
				$project_type = ' AND p.project_type = "solo"';
			}
			else
			{
				$project_type = ' AND p.project_type != "solo"';
			}
		}

		/* Language */

		$language = ' ';
		$language_clause = ' ';
		$section_language_clause = ' ';
		$recorded_language = (int)$params['recorded_language'];
		if ($recorded_language !== 0)
		{
			//$language = ' AND p.language_id = ' . $params['recorded_language'] ;

			// Projects having at least one section in that language.
			$section_project_ids = $this->_get_projects_by_section_language($recorded_language);
			//$language_clause = ' AND p.id IN (' . implode(',', $section_project_ids) . ') ';

			$language_clause = ' AND ( p.language_id = ' . $recorded_language . ' OR p.id IN (' . implode(',', $section_project_ids) . ') ) ';
			$section_language_clause = '
				AND ( p.language_id = ' . $recorded_language . '
					OR st.section_language_id = ' . $recorded_language . ') ';
		}


		/* Genre */

		$genre_clause = '';
		if (!empty($params['genre_id']))
		{
			$this->load->model('genre_model');
			$genre = $this->genre_model->get($params['genre_id']);

			$genre_clause	 = 	' JOIN project_genres pg ON (pg.project_id = p.id)  ';
			$genre_clause	 .=	' JOIN genres g ON (g.id = pg.genre_id) AND g.lineage LIKE  "'. $genre->lineage . '%"';
		}

		/* Keywords */

		$keyword_clause = '';
		if (!empty($params['keywords']))
		{
			$keywords = explode(' ', $params['keywords']); //maybe preg_match if extra spaces cause trouble - thinnk we're ok
			$keywords = array_map('trim', $keywords);  // clean it up

			$escaped_keywords = [];
			foreach ($keywords as $keyword)
				$escaped_keywords[] = $this->db->escape($keyword);
			$in_keywords = implode(", ", $escaped_keywords);

			$keyword_clause	 =	' JOIN project_keywords pk ON (pk.project_id = p.id) ';
			$keyword_clause	 .= ' JOIN keywords k ON (k.id = pk.keyword_id) AND k.value IN ('. $in_keywords .')  ';

		}

		//Do not combine these - they are separate conditions that both must be true
		$author_clause = '';
		$section_author_clause = '';
		if (!empty($params['author']))
		{
			$params['author'] = str_replace(' ', '%', $params['author']);

			$project_ids = $this->_get_projects_by_author($params['author']);
			$section_project_ids = $this->_get_projects_by_section_author($params['author']);

			$project_ids = array_merge($project_ids, $section_project_ids);
			$author_clause = ' AND p.id IN (' . implode(',', $project_ids) . ') ';


			$author_ids = $this->_get_author_ids($params['author']);
			$section_author_clause = ' AND ( st.source_id IN (' . implode(',', $project_ids) . ') AND st.section_author_id IN  (' . implode(',', $author_ids) . ') )  ';
		}

		$reader_clause = '';
		if (!empty($params['reader']))
		{
			$exact_match = (isset($params['exact_match'])) ? true: false;
			$project_ids = $this->_get_projects_by_reader($params['reader'], $exact_match);
			$reader_clause = ' AND p.id IN (' . implode(',', $project_ids) . ') ';
		}

		// ======================================================================================================================================



		// Combine all of our results as a sub-query, so we can count the results in one place.
		$sql .= 'WITH results AS (';


			//***** Projects from compilations *****//

				$cols = array();

				$cols[] = 'st.source_table';
				$cols[] = 'st.search_field';
				$cols[] = 'p.title';
				$cols[] = 'COALESCE(p.date_catalog, "2001-01-01")';
				$cols[] = 'p.id';
				$cols[] = 'p.project_type';
				$cols[] = 'p.title_prefix';
				$cols[] = 'p.url_librivox';
				$cols[] = 'p.url_forum';
				$cols[] = 'p.status';
				$cols[] = 'p.coverart_thumbnail';
				$cols[] = 'p.zip_url';
				$cols[] = 'p.zip_size';
				$cols[] = 'l.language';
				$cols[] = 'l.id AS language_id';
				$cols[] = 'l.two_letter_code';

				$sql .= '

				SELECT "title" AS blade, ' . implode(',' , $cols) . '

				FROM search_table st

				JOIN projects p ON (p.id = st.source_id AND st.source_table IN  ("projects" ) )

				JOIN languages l ON (l.id = p.language_id ' . $language . ')

				' . $genre_clause . '
				' . $keyword_clause . '


				WHERE 1
				' . $status . '
				' . $project_type . '
				' . $title . '
				' . $author_clause . '
				' . $reader_clause . '
				' . $language_clause . ' ';

			//***** Sections from compilations *****//

				$cols = array();

				$cols[] = 'st.source_table';
				$cols[] = 'st.search_field';
				$cols[] = 'p.title';
				$cols[] = 'COALESCE(p.date_catalog, "2001-01-01")';
				$cols[] = 'p.id';
				$cols[] = 'p.project_type';
				$cols[] = 'p.title_prefix';
				$cols[] = 'p.url_librivox';
				$cols[] = 'p.url_forum';
				$cols[] = 'p.status';
				$cols[] = 'p.coverart_thumbnail';
				$cols[] = 'p.zip_url';
				$cols[] = 'p.zip_size';
				$cols[] = 'l.language';
				$cols[] = 'l.id AS language_id';
				$cols[] = 'l.two_letter_code';

				$sql .= '
					UNION

				SELECT "title" AS blade, ' . implode(',' , $cols) . '

				FROM search_table st

				JOIN projects p ON (p.id = st.source_id AND st.source_table IN  ( "sections" ) )

				JOIN languages l ON (l.id = p.language_id ' . $language . ')

				' . $genre_clause . '
				' . $keyword_clause . '


				WHERE 1
				' . $status . '
				' . $project_type . '
				' . $title . '
				' . $section_author_clause . '
				' . $reader_clause . '
				' . $section_language_clause . ' ';


			//* Groups *//

				unset($cols);

				$cols[] = '""';
				$cols[] = '""';
				$cols[] = 'gr.name AS title';
				$cols[] = '"2001-01-01"';
				$cols[] = 'gr.id';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';
				$cols[] = '""';

				$sql .= '
					UNION

					SELECT "group" AS blade,' . implode(',' , $cols) . '

					FROM search_table st

					JOIN groups gr ON (gr.id = st.source_id AND st.source_table = "groups" )

					JOIN group_projects gp ON (gp.group_id = gr.id)

					JOIN projects p ON (p.id = gp.project_id)

					JOIN languages l ON (l.id = p.language_id ' . $language . ')

				' . $genre_clause . '
				' . $keyword_clause . '


				WHERE 1
				' . $status . '
				' . $project_type . '
				' . $title . '
				' . $author_clause . '
				' . $reader_clause . '
				' . $language_clause . ' ';


				//***** finalize query parts *****//

				$sql .= ") SELECT *, COUNT(*) OVER() as full_count FROM results";

				$sql .= ($params['sort_order'] == 'catalog_date') ? ' ORDER BY 5 DESC ' : ' ORDER BY 4 ASC ';

				$sql .= ' LIMIT ' . $params['offset'] . ', ' . $params['limit'];

				$query = $this->db->query($sql);
				//echo $this->db->last_query();

				return $query->result_array();


	}


	/*
		Utility functions
	*/

	// return an array of project ids where author or pseudonym linked
	private function _get_projects_by_author($author)
	{
		$sql = 'SELECT pa.project_id
				FROM project_authors pa
				JOIN authors a ON (pa.author_id = a.id)
				WHERE CONCAT(a.first_name, " " , a.last_name) LIKE ?
				AND a.linked_to = 0
				UNION
				SELECT pa.project_id
				FROM project_authors pa
				JOIN authors a ON (pa.author_id = a.id)
				JOIN author_pseudonyms ap ON (ap.author_id = a.id)
				WHERE CONCAT(ap.first_name, " " , ap.last_name) LIKE ?
				AND a.linked_to = 0	';
		$author_like = "%$author%";
		$query = $this->db->query($sql, array($author_like, $author_like));
		return $this->ids($query, 'project_id');
	}


	// return an array of project ids where author or pseudonym linked to section
	private function _get_projects_by_section_author($author)
	{
		$sql = 'SELECT st.source_id
				FROM search_table st
				JOIN authors a ON (st.section_author_id = a.id)
				WHERE CONCAT(a.first_name, " " , a.last_name) LIKE ?
				AND a.linked_to = 0
				UNION
				SELECT st.source_id
				FROM search_table st
				JOIN authors a ON (st.section_author_id = a.id)
				JOIN author_pseudonyms ap ON (ap.author_id = a.id)
				WHERE CONCAT(ap.first_name, " " , ap.last_name) LIKE ?
				AND a.linked_to = 0	';
		$author_like = "%$author%";
		$query = $this->db->query($sql, array($author_like, $author_like));
		return $this->ids($query, 'source_id');
	}


	// return an array of author ids where author or pseudonym matches search
	private function _get_author_ids($author)
	{
		$sql = 'SELECT a.id
				FROM authors a
				WHERE CONCAT(a.first_name, " " , a.last_name) LIKE ?
				AND a.linked_to = 0
				UNION
				SELECT a.id
				FROM authors a
				JOIN author_pseudonyms ap ON (ap.author_id = a.id)
				WHERE CONCAT(ap.first_name, " " , ap.last_name) LIKE ?
				AND a.linked_to = 0	';
		$author_like = "%$author%";
		$query = $this->db->query($sql, array($author_like, $author_like));
		return $this->ids($query);
	}

	private function _get_reader_ids($reader, $exact_match)
	{
		$sql = 'SELECT u.id
				FROM users u
				WHERE u.display_name LIKE ?
				OR u.username LIKE ?';
		$reader_like =  $exact_match ? $reader : "%$reader%";
		$query = $this->db->query($sql, array($reader_like, $reader_like));
		return $this->ids($query);
	}


	private function _get_projects_by_section_language($language_id)
	{
		$sql = 'SELECT st.source_id
				FROM search_table st
				WHERE section_language_id = ? ';
		$query = $this->db->query($sql, array($language_id));
		return $this->ids($query, 'source_id');
	}


	private function _get_projects_by_reader($reader, $exact_match)
	{
		$sql = 'SELECT pr.project_id
				FROM project_readers pr
				WHERE pr.display_name LIKE ?
				OR pr.username LIKE ?';
		$reader_like =  $exact_match ? $reader : "%$reader%";
		$query = $this->db->query($sql, array($reader_like, $reader_like));
		return $this->ids($query, 'project_id');
	}


	private function ids($query, $id_column = 'id')
	{
		$ids = [];
		foreach ($query->result('array') as $key => $value)
		{
			$ids[] = $value[$id_column];
		}
		if (empty($ids))
		{
			$ids[] = 0; // To prevent SQL syntax errors of the form "WHERE id IN ()".
		}
		return $ids;
	}
}
