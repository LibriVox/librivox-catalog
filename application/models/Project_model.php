<?php

require_once(APPPATH.'libraries/Underscore.php');

class Project_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    function get_projects($params)
    {
        $sql = 'SELECT DISTINCT p.id, p.title, p.status, p.url_forum, p. url_librivox ' ;

        if (!empty($params['user_projects']))
        {
            $user_projects = $this->db->escape($params['user_projects']);
            $sql .= '
                , IF (p.person_bc_id        = '.$user_projects. ', "x", "") AS bc
                , IF (p.person_mc_id        = '.$user_projects. ', "x", "") AS mc
                , IF (p.person_pl_id        = '.$user_projects. ', "x", "") AS pl
                , IF (reader_data.reader_id = '.$user_projects. ', "x", "") AS reader ';
        }

        $sql .= ' FROM ' . $this->_table . ' p ';

        if (!empty($params['project_search']))
        {
            //here's where it gets hairy... find the authors, get the project ids, include those
            $project_ids_by_author = $this->get_project_ids_by_author($params['project_search']);
            $project_search = $this->db->escape('%' . $params['project_search'] . '%');
            $sql .= '
                WHERE (   p.id    LIKE '.$project_search.'
                       OR p.title LIKE '.$project_search.'
                       OR p.id    IN  ('.$project_ids_by_author.') ) ';
        }

        if (!empty($params['user_projects']))
        {
            $user_projects = $this->db->escape($params['user_projects']);

            $sql .= '   LEFT OUTER JOIN (SELECT s.project_id AS section_project_id, sr.reader_id AS reader_id
                        FROM sections s
                        JOIN section_readers sr ON (sr.section_id = s.id)
                        WHERE sr.reader_id = '.$user_projects.') as reader_data ON (reader_data.section_project_id = p.id)' ;


            // as other volunteer
            $sql .= '
                WHERE (p.person_bc_id    = '.$user_projects.'
                OR p.person_altbc_id     = '.$user_projects.'
                OR p.person_mc_id        = '.$user_projects.'
                OR p.person_pl_id        = '.$user_projects.'
                OR reader_data.reader_id = '.$user_projects.')';
        }

        $sql .= ' ORDER BY p.title';

        $query = $this->db->query($sql);
        return $query->result();

    }

    function get_latest_releases($limit=10)
    {
        $sql = 'SELECT p.id, p.title_prefix, p.title, p.url_librivox,
                    p.description, p.project_type, p.coverart_thumbnail,
                    p.date_catalog,
                    COALESCE(l.two_letter_code, l.three_letter_code)
                    AS language_code, language
                FROM projects p
                JOIN languages l ON (l.id = p.language_id)
                WHERE p.status = "complete"
                ORDER BY p.date_catalog DESC
                LIMIT 0, ?';

        $query = $this->db->query($sql, array($limit));
        return $query->result();
    }

    function get_project_ids_by_author($search)
    {
        $sql = 'SELECT pa.project_id
        FROM project_authors pa
        JOIN authors a ON (a.id = pa.author_id)
        WHERE (a.first_name LIKE ? OR a.last_name LIKE ?)
        AND pa.type = "author"';

        $search_like = "%$search%";
        $query = $this->db->query($sql, array($search_like, $search_like));
        $project_ids = $query->result();

        if (empty($project_ids)) return 0;

        return  implode( ","  , __()->pluck($project_ids, 'project_id'));

    }

    // seems this is a dupe of down below - the lower one should no longer be in use, but just grep & clean up when time
    function get_project_authors($project_id)
    {
        $sql = 'SELECT CONCAT(a.first_name, " ", a.last_name) as author, a.id, a.dob, a.dod
        FROM authors a
        JOIN project_authors pa ON (a.id = pa.author_id)
        WHERE pa.project_id = ?
        AND pa.type = "author"';

        $query = $this->db->query($sql, array($project_id));
        return $query->result();

    }


    // For catalog and Archive pages of solo projects.
    //   Not every solo project has exactly one reader, and the solo reader is not always the BC.  We don't have a `person_soloreader_id` column.
    //   Given that, MCs are comfortable with this 'pick first result' heuristic, which is how the catalog pages have functioned up to now.
    function get_solo_reader($project_id)
    {
        $sql = 'SELECT u.id AS reader_id, u.display_name
        FROM users u
        JOIN section_readers sr ON (sr.reader_id = u.id)
        JOIN sections s ON (s.id = sr.section_id)
        WHERE s.project_id = ?
        LIMIT 1';

        $query = $this->db->query($sql, array($project_id));

        return $query->row_array();

    }





    // for catalog author page
    function get_projects_by_author($params)
    {
        // This is rapidly becoming what we should have on the Librivox_search lib instead...

        // NOTE!! While we restrict by author here, to avoid duplicate projects & to speed the query, we don't collect author data but rather will loop over that in controller

        if (!isset($params['sub_category']))
        {
            $params['sub_category'] = '';
        }

        $where_project_type = '';
        if (!empty($params['project_type']) && $params['project_type'] != 'either')
        {
            if ($params['project_type'] == 'solo')
            {
                $where_project_type = ' AND p.project_type = "solo"';
            }
            else
            {
                $where_project_type = ' AND p.project_type != "solo"';
            }
        }

        $bindings = array();
        $sql = '
                SELECT "project" AS primary_type, p.id AS primary_key, p.id, p.project_type, p.title_prefix, p.title,
                COALESCE(p.date_catalog, "2001-01-01" ), p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, l.language , p.url_forum
                FROM projects p
                JOIN project_authors pa ON (pa.project_id = p.id)
                JOIN languages l ON (l.id = p.language_id) ';

                if ($params['sub_category'] == 'genre' && $params['primary_key'])
                {
                    $sql .=  ' JOIN project_genres pg ON (pg.project_id = p.id )' ;
                }

                $sql .=    ' WHERE 1 ';

                if ($params['sub_category'] == 'language' && $params['primary_key'])
                {
                    $sql .=  ' AND l.id = ?';
                    $bindings = array_merge($bindings, array($params['primary_key']));
                }
                if ($params['sub_category'] == 'genre' && $params['primary_key'])
                {
                    $sql .=  ' AND pg.genre_id = ?';
                    $bindings = array_merge($bindings, array($params['primary_key']));
                }

                if ($params['author_id'])
                {
                    $sql .=  ' AND pa.author_id = ?';
                    $bindings = array_merge($bindings, array($params['author_id']));
                }

                $sql .= $where_project_type;


        $sql .= '
                UNION
                SELECT "section" AS primary_type, s.id AS primary_key, p.id, p.project_type, "", CONCAT(s.title, " (in ", COALESCE(p.title_prefix, "" ), " ", p.title, ")") as title,
                COALESCE(p.date_catalog, "2001-01-01" ), p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, l.language , p.url_forum
                FROM projects p
                JOIN sections s ON (p.id = s.project_id)
                JOIN languages l ON (l.id = p.language_id) ';

                if ($params['sub_category'] == 'genre' && $params['primary_key'])
                {
                    $sql .=  ' JOIN project_genres pg ON (pg.project_id = p.id )' ;
                }

                $sql .= ' WHERE p.is_compilation = 1 ';

                if ($params['sub_category'] == 'language' && $params['primary_key'])
                {
                    $sql .=  ' AND (l.id = ? OR s.language_id = ? )' ;
                    $bindings = array_merge($bindings, array($params['primary_key'], $params['primary_key']));
                }
                if ($params['sub_category'] == 'genre' && $params['primary_key'])
                {
                    $sql .=  ' AND pg.genre_id = ?';
                    $bindings = array_merge($bindings, array($params['primary_key']));
                }
                if ($params['author_id'])
                {
                    $sql .=    ' AND s.author_id = ?';
                    $bindings = array_merge($bindings, array($params['author_id']));
                }

                $sql .= $where_project_type;

                //skip empty sections this way
                $sql .= ' AND p.status = "' . PROJECT_STATUS_COMPLETE . '"';

        $order = ($params['search_order'] == 'catalog_date') ? ' 7 DESC' : ' 6 ASC '; //release date, else alpha as default

        $sql .= ' ORDER BY ' . $order ;

        $sql .= ' LIMIT ? , ?';
        $bindings = array_merge($bindings, array($params['offset'], $params['limit']));

        $query = $this->db->query($sql, $bindings);
        //echo $this->db->last_query();

        return $query->result_array();
    }


    // for catalog front page - Title menu. simplified browsing version
    function get_projects_for_title_menu($params)
    {
        // This is rapidly becoming what we should have on the Librivox_search lib instead...

        // NOTE!! While we restrict by author here, to avoid duplicate projects & to speed the query, we don't collect author data but rather will loop over that in controller

        $where_project_type = '';
        if (!empty($params['project_type']) && $params['project_type'] != 'either')
        {
            if ($params['project_type'] == 'solo')
            {
                $where_project_type = ' AND p.project_type = "solo"';
            }
            else
            {
                $where_project_type = ' AND p.project_type != "solo"';
            }
        }

        $sql = '
                SELECT "project" AS primary_type, p.id AS primary_key, p.id, p.project_type, p.title_prefix, p.title,
                COALESCE(p.date_catalog, "2001-01-01" ), p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, l.language, p.url_forum

                FROM projects p

                JOIN languages l ON (l.id = p.language_id) ';

                $sql .= sprintf(' WHERE p.status = "%s"', PROJECT_STATUS_COMPLETE);

                $sql .= $where_project_type;


        $order = ($params['search_order'] == 'catalog_date') ? ' 7 DESC' : ' 6 ASC '; //release date, else alpha as default

        $sql .= sprintf(' ORDER BY %s LIMIT %d, %d', $order, $params['offset'], $params['limit']);

        $query = $this->db->query($sql);

        //echo $this->db->last_query();

        return $query->result_array();
    }





    function get_projects_by_reader($params)
    {
        $sql = 'SELECT p.id, p.project_type, p.title_prefix, p.title, p.date_catalog, p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size,
                    l.two_letter_code, l.language , p.url_forum
                FROM projects p
                JOIN project_readers pr ON (pr.project_id = p.id)
                JOIN languages l ON (l.id = p.language_id)
                WHERE pr.reader_id = ?';

        $project_type = $params['project_type'];
        if (!empty($project_type) && $project_type != 'either')
        {
            $sql .= ($project_type == 'solo')
                ? ' AND p.project_type = "solo"'
                : ' AND p.project_type != "solo"';
        }

        $order = ($params['search_order'] == 'catalog_date')
            ? ' 5 DESC ' // release date
            : ' 4 ASC '; // title

        $sql .= ' ORDER BY ' . $order;

        $offset = 0 + $params['offset'];
        $limit = 0 + $params['limit'];
        $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql, array($params['reader_id']));

        return $query->result_array();
    }





    function get_projects_by_group($params)
    {

        $sql = 'SELECT p.id, p.project_type, p.title_prefix, p.title, p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size,
                     l.two_letter_code, l.language, p.url_forum
                FROM projects p
                JOIN group_projects gp ON (gp.project_id = p.id)
                JOIN languages l ON (l.id = p.language_id)
                WHERE gp.group_id = ? ' ;

        $sql .= ' ORDER BY p.title ASC ';

        $offset = 0 + $params['offset'];
        $limit = 0 + $params['limit'];
        $sql .= ' LIMIT ' . $offset . ', ' . $limit;


        $query = $this->db->query($sql, array($params['group_id']));

        return $query->result_array();

    }


    function get_frozen_projects()
    {
        $sql = 'SELECT p.*
                FROM projects p
                WHERE p.date_catalog BETWEEN DATE_SUB(curdate(), INTERVAL 16 DAY) AND DATE_SUB(curdate(), INTERVAL 15 DAY)';

        $query = $this->db->query($sql);

        return $query->result();

    }
    
    
    public function get_projects_by_keywords_id($params)
    {
	// If calls to this function do not pass a project_type_description parameter,
	// provide a sensible default
		
	$keyword_id = $params['keywords_id']; 
        $offset = 0 + $params['offset'];
        $limit = 0 + $params['limit'];      
        
        $sql = 'select p.id, p.title_prefix, p.title, COALESCE(p.date_catalog, "2001-01-01" ) as safe_release_date, 
        p.url_librivox, p.status, p.coverart_thumbnail, 	        p.zip_url, p.zip_size, l.two_letter_code, l.language, p.url_forum, p.project_type
	FROM keywords k
	JOIN project_keywords pk ON (k.id = pk.keyword_id)
	JOIN projects p on (pk.project_id = p.id)
	JOIN languages l ON (l.id = p.language_id) ';
	$sql .= 'WHERE k.id = ? ';
	
	
	// It is likely that either $params['project_type_description'] will not exist as a key at all,
	// or that if it does exist, it will contain the values of 'either','solo' or 'group', matching values passed
	// from Javascript embedded in the search page footer. Of these values, only 'solo' matches a value actually to be
	// found in the projects.project_type field.

	if (array_key_exists('project_type_description', $params)) 
	{
		if ($params['project_type_description'] == 'group')
		{
			$sql .= 'AND p.project_type != "solo" '; 
		}
		elseif ($params['project_type_description'] == 'solo')
		{
			$sql .= 'AND p.project_type = "solo" '; 
		}
	}

	if ($params['search_order'] == 'catalog_date')
	{
		$sql .= ' ORDER BY safe_release_date DESC ';
	} else 	
        {
        	$sql .= ' ORDER BY p.title ASC ';
        }
        $sql .= ' LIMIT ? , ? ';
        $query = $this->db->query($sql, array($keyword_id, $offset, $limit));

        return $query->result_array();

    }
    
    
    public function get_keywords_and_statistics_by_project($project_id)
    {        
        $sql = '
        SELECT keywords.id, keywords.value, keywords.count as keyword_count
	FROM keywords
		JOIN project_keywords 
		ON keywords.id = project_keywords.keyword_id
		WHERE project_keywords.project_id = ?
		ORDER BY keywords.count DESC' ;

        $query = $this->db->query($sql, array($project_id));
        if ($query->num_rows() > 0) return $query->result_array();
        return '';
    }
    
    public function get_keywords_count_by_project($project_id)
    {        
        $sql = '
        SELECT COUNT(keywords.id) as keywords_number
	FROM keywords
		JOIN project_keywords 
		ON keywords.id = project_keywords.keyword_id
		WHERE project_keywords.project_id = ?';

        $query = $this->db->query($sql, array($project_id));
        return $query->row();
    }



    /*

      ---  functions below this line are from the old system, and may or may not be in use. will do a grep later, but if you are reading this message,
    it may not have been cleaned up completely yet

    */

    public function search($where)
    {

    	if (!empty($where['projectid']))
    	{
    		$this->db->where(array('id'=>$where['projectid']));
    	}

    	if (!empty($where['projectname']))
    	{
            foreach (explode(' ', $where['projectname']) as $word) {
                $this->db->like('title', $word);
            }
    	}

    	if (!empty($where['status']))
    	{
    		$this->db->like(array('status'=>$where['status']));
    	}

    	$query =  $this->db->order_by('status')->order_by('title')->get($this->_table);

    	if ($query->num_rows() > 0)
    	{
    		return $query->result_array();
    	}
    	return false;
    }


    public function get_authors_by_project($project_id, $type= 'author', $include_sections = false)
    {
        $sql = '
        SELECT a.*
        FROM project_authors pa
        JOIN authors a ON (pa.author_id = a.id)
        WHERE pa.project_id = ?
        AND pa.type = ? ';
        $bind = array($project_id, $type);

        if ($include_sections and $this->get($project_id)->is_compilation) {
            $sql .= '
            UNION
            SELECT a.*
            FROM sections s
            JOIN authors a ON (s.author_id = a.id)
            WHERE s.project_id = ?';

            $bind[] = $project_id;
        }

        $query = $this->db->query($sql, $bind);

        if ($query->num_rows() > 0) return $query->result_array();

        return false;
    }

    public function get_keywords_by_project($project_id)
    {
        $sql = '
        SELECT k.*
        FROM project_keywords pk
        JOIN keywords k ON (pk.keyword_id = k.id)
        WHERE pk.project_id = ?  ';

        $query = $this->db->query($sql, array($project_id));

        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $key => $row) {
                 $keywords[] = $row['value'];
            }
            return implode(', ', $keywords);
        }
        return '';
    }

    public function get_genres_by_project($project_id, $return = 'name', $return_type = 'parsed_array')
    {
        $sql = '
        SELECT g.*
        FROM project_genres pg
        JOIN genres g ON (pg.genre_id = g.id)
        WHERE pg.project_id = ?  ';

        $query = $this->db->query($sql, array($project_id));

        if ( $return_type == 'parsed_array')
        {
            if ($query->num_rows() > 0)
            {
                    foreach ($query->result_array() as $key => $row) {
                         $genres[] = $row[$return];
                    }
                    return implode(', ', $genres);
            }

            return '';
        }
        else
        {
            return $query->result_array();
        }
    }


   // for catalog reader page
     /* prolly garbage now */
    function get_projects_by_reader_status($params)
    {
        $status_where = ($params['status'] == 'complete') ? ' AND p.status = "complete" ': ' AND p.status != "complete" ';
        $sql = 'SELECT p.id, p.project_type, p.title_prefix, p.title, p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, l.two_letter_code
                FROM projects p
                JOIN project_readers pr ON (pr.project_id = p.id)
                JOIN languages l ON (l.id = p.language_id)
                WHERE pr.reader_id = ? ' .
                $status_where .
                'ORDER BY p.title ASC ';
                //LIMIT '. $params['search_offset']. ',' . $params['search_limit'];

        $query = $this->db->query($sql, array($params['user_id']));

        return $query->result_array();
    }
}
