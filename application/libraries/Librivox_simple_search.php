<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Librivox_simple_search{

	//this is searching logic for the main, public-facing catalog search
	// may be some complications, so keeping it all together here


	public function __construct()
	{

	}

	public function __get($var)
	{
		return get_instance()->$var;
	}

	function simple_search($params)
	{
		$like_title = $this->db->escape('%' . str_replace(' ', '%', $params['title']) . '%');
		$like_author = $this->db->escape('%' . str_replace(' ', '%', $params['author']) . '%');
		$like_reader = $this->db->escape('%' . str_replace(' ', '%', $params['reader']) . '%');
		$sort_order = $params['sort_order'];
		$offset = (int)$params['offset'];
		$limit = (int)$params['limit'];


		 // Combine all of our results as a sub-query, so we can count the results in one place.
		$sql = 'WITH results AS (';


		//***** Titles, Sections from compilations *****//

			$cols = array();

			$cols[] = 'st.source_table';
			$cols[] = 'st.search_field';
			$cols[] = 'p.title AS sort_field';
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
			$cols[] = '"" AS author_id';
			$cols[] = '"" AS first_name';
			$cols[] = '"" AS last_name';
			$cols[] = '"" AS dob';
			$cols[] = '"" AS dod';
			$cols[] = '"" AS meta_complete';
			$cols[] = '"" AS meta_in_progress';
			$cols[] = '"" AS reader_id';
			$cols[] = '"" AS display_name';
			$cols[] = '"" AS username';
			$cols[] = 'l.language';
			$cols[] = 'l.id AS language_id';
			$cols[] = 'l.two_letter_code';

			// q is in the title, and not in a group. Joins language & author for fields.

			$sql .= '
					SELECT "title" AS blade, ' . implode(',' , $cols) . '

					FROM search_table st

					JOIN projects p ON (p.id = st.source_id AND st.source_table IN  ("projects", "sections" ) )

					JOIN languages l ON (l.id = p.language_id )

					WHERE st.`search_field` LIKE ' . $like_title;



		//***** Groups *****//

			unset($cols);

			$cols[] = '""';
			$cols[] = '""';
			$cols[] = 'gr.name AS sort_field';
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


			// q is in the project title, which is part of a group. author not required for select

			$sql .= '

					UNION

					SELECT "group" AS blade,' . implode(',' , $cols) . '

					FROM search_table st

					JOIN groups gr ON (gr.id = st.source_id AND st.source_table = "groups" )

					WHERE st.`search_field` LIKE ' . $like_title;


		//*****  Readers ***** //

			unset($cols);

			$cols[] = '"readers" AS source_table';
			$cols[] = '"" AS search_field';
			$cols[] = 'pr.display_name AS sort_field';
			$cols[] = '"" AS title';
			$cols[] = '"" AS date_catalog';
			$cols[] = '"" AS id';
			$cols[] = '"" AS project_type';
			$cols[] = '"" AS title_prefix';
			$cols[] = '"" AS url_librivox';
			$cols[] = '"" AS url_forum';
			$cols[] = '"" AS status';
			$cols[] = '"" AS coverart_thumbnail';
			$cols[] = '"" AS zip_url';
			$cols[] = '"" AS zip_size';
			$cols[] = '"" AS author_id';
			$cols[] = '"" AS first_name';
			$cols[] = '"" AS last_name';
			$cols[] = '"" AS dob';
			$cols[] = '"" AS dod';
			$cols[] = '"" AS meta_complete';
			$cols[] = '"" AS meta_in_progress';

			$cols[] = 'pr.id AS reader_id';
			$cols[] = 'pr.display_name';
			$cols[] = 'pr.username';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';


			$sql .= '

					UNION

					SELECT "reader" AS blade, ' . implode(',' , $cols) . '

					FROM users pr

					WHERE pr.display_name LIKE ' . $like_reader .'

					OR pr.username LIKE ' . $like_reader;


		//***** Authors and pseudonyms *****//

			unset($cols);

			$cols[] = '"authors" AS source_table';
			$cols[] = '"" AS search_field';
			$cols[] = 'a.last_name AS sort_field';
			$cols[] = '"" AS title';
			$cols[] = '"" AS date_catalog';
			$cols[] = '"" AS id';
			$cols[] = '"" AS project_type';
			$cols[] = '"" AS title_prefix';
			$cols[] = '"" AS url_librivox';
			$cols[] = '"" AS url_forum';
			$cols[] = '"" AS status';
			$cols[] = '"" AS coverart_thumbnail';
			$cols[] = '"" AS zip_url';
			$cols[] = '"" AS zip_size';

			$cols[] = 'a.id AS author_id';
			$cols[] = 'a.first_name';
			$cols[] = 'a.last_name';
			$cols[] = 'a.dob';
			$cols[] = 'a.dod';

			$cols[] = 'a.meta_complete';
			$cols[] = 'a.meta_in_progress';

			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';

			$sql .= '

					UNION

					SELECT "author" AS blade, ' . implode(',' , $cols) . '

					FROM authors a

					WHERE CONCAT(a.first_name, " " , a.last_name) LIKE ' . $like_author .'

					AND a.linked_to = 0	';

			// Also do pseudonyms - psuedonym name, where either psuedonym or real author name matches search

			unset($cols);

			$cols[] = '"pseudonyms" AS source_table';
			$cols[] = '"" AS search_field';
			$cols[] = 'ap.last_name AS sort_field';
			$cols[] = '"" AS title';
			$cols[] = '"" AS date_catalog';
			$cols[] = '"" AS id';
			$cols[] = '"" AS project_type';
			$cols[] = '"" AS title_prefix';
			$cols[] = '"" AS url_librivox';
			$cols[] = '"" AS url_forum';
			$cols[] = '"" AS status';
			$cols[] = '"" AS coverart_thumbnail';
			$cols[] = '"" AS zip_url';
			$cols[] = '"" AS zip_size';

			$cols[] = 'a.id AS author_id';
			$cols[] = 'ap.first_name';
			$cols[] = 'ap.last_name';
			$cols[] = 'a.dob';
			$cols[] = 'a.dod';

			$cols[] = 'a.meta_complete';
			$cols[] = 'a.meta_in_progress';

			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';


			$sql .= '

					UNION

					SELECT "author", ' . implode(',' , $cols) . '

					FROM author_pseudonyms ap

					JOIN authors a ON (ap.author_id = a.id)

					WHERE ( CONCAT(ap.first_name, " " , ap.last_name) LIKE ' . $like_author .'

					OR CONCAT(a.first_name, " " , a.last_name) LIKE ' . $like_author .' )

					AND a.linked_to = 0 ';


			// Also do authors of pseudonyms

			unset($cols);

			$cols[] = '"pseudonyms" AS source_table';
			$cols[] = '"" AS search_field';
			$cols[] = 'a.last_name AS sort_field';
			$cols[] = '"" AS title';
			$cols[] = '"" AS date_catalog';
			$cols[] = '"" AS id';
			$cols[] = '"" AS project_type';
			$cols[] = '"" AS title_prefix';
			$cols[] = '"" AS url_librivox';
			$cols[] = '"" AS url_forum';
			$cols[] = '"" AS status';
			$cols[] = '"" AS coverart_thumbnail';
			$cols[] = '"" AS zip_url';
			$cols[] = '"" AS zip_size';

			$cols[] = 'a.id AS author_id';
			$cols[] = 'a.first_name';
			$cols[] = 'a.last_name';
			$cols[] = 'a.dob';
			$cols[] = 'a.dod';

			$cols[] = 'a.meta_complete';
			$cols[] = 'a.meta_in_progress';

			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';
			$cols[] = '""';


			$sql .= '

					UNION

					SELECT "author", ' . implode(',' , $cols) . '

					FROM author_pseudonyms ap

					JOIN authors a ON (ap.author_id = a.id)

					WHERE CONCAT(ap.first_name, " " , ap.last_name) LIKE ' . $like_author .'

					AND a.linked_to = 0 ';



		//***** finalize query parts *****//

			$sql .= ") SELECT *, COUNT(*) OVER() as full_count FROM results";

			$sql .= ($sort_order == 'catalog_date')
				? ' ORDER BY FIELD(blade, "title", "group", "reader", "author") , 6 DESC '
				: ' ORDER BY FIELD(blade, "title", "group", "reader", "author") , 4 ASC ';

			$sql .= ' LIMIT ' . $offset . ', ' . $limit;

			$query = $this->db->query($sql);
			//echo $this->db->last_query();

			return $query->result_array();

	}

}
