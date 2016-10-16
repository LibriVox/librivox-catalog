<?php

require_once(APPPATH.'libraries/underscore.php');

class Project_model extends MY_Model
{
    public function __construct()
    {       
        parent::__construct();
    }

    function get_projects($params)
    {
        //$limit = 'LIMIT 0, 500';
        $limit = '';

        $sql = 'SELECT DISTINCT p.id, p.title, p.status, p.url_forum, p. url_librivox ' ;

        if (!empty($params['user_projects']))
        { 
           $sql .= ' , IF (p.person_bc_id = '.$params['user_projects']. ', "x", "") AS bc,
            IF (p.person_mc_id = '.$params['user_projects']. ', "x", "") AS mc,
            IF (p.person_pl_id = '.$params['user_projects']. ', "x", "") AS pl,
            IF (reader_data.reader_id = '.$params['user_projects']. ', "x", "") AS reader ';
        }

        $sql .= ' FROM ' . $this->_table . ' p ';

        if (!empty($params['project_search']))
        {
            //here's where it gets hairy... find the authors, get the project ids, include those
            $project_ids_by_author = $this->get_project_ids_by_author($params['project_search']);

            $sql .= ' WHERE (p.id LIKE "%' .$params['project_search']. '%" OR  p.title LIKE "%' .$params['project_search']. '%" OR p.id IN ('.$project_ids_by_author.') ) ';
        } 

        if (!empty($params['user_projects']))
        {
            
            //$sql .= ' LEFT OUTER JOIN sections s ON (s.project_id = p.id) 
            //        LEFT OUTER JOIN section_readers sr ON (sr.section_id = s.id) ';

            $sql .= '   LEFT OUTER JOIN (SELECT s.project_id AS section_project_id, sr.reader_id AS reader_id
                        FROM sections s
                        JOIN section_readers sr ON (sr.section_id = s.id) 
                        WHERE sr.reader_id = '.$params['user_projects']. ') as reader_data ON (reader_data.section_project_id = p.id)' ;       


            // as other volunteer
            $sql .= ' WHERE (p.person_bc_id = '.$params['user_projects']. ' 
                OR p.person_altbc_id = '.$params['user_projects']. ' 
                OR p.person_mc_id = '.$params['user_projects']. '
                OR p.person_pl_id = '.$params['user_projects']. '
                OR reader_data.reader_id = '.$params['user_projects']. ')';                
        }           

        $sql .= ' ORDER BY p.title '. $limit;

        $query = $this->db->query($sql);
        return $query->result();

    }

    function get_lastest_releases($limit=10)
    {
        $sql = 'SELECT DISTINCT p.id, p.title_prefix, p.title, p.url_librivox, p.description, p.project_type, p.coverart_thumbnail, p.date_catalog, COALESCE(l.two_letter_code, l.three_letter_code) AS language_code, language
                FROM projects p 
                JOIN languages l ON (l.id = p.language_id) 
                WHERE p.status = "complete" 
                ORDER BY p.date_catalog DESC 
                LIMIT 0, '. $limit ;

        $query = $this->db->query($sql);
        return $query->result();                
    }

    function get_projects_summary($user_id, $status='active')
    {
        // TODO: confirm this - may be getting multiple rows
        $sql = 'SELECT  COUNT(DISTINCT p.id) AS total_projects,   
            IFNULL(SUM(IF (p.person_bc_id = '.$user_id. ', 1, 0)), 0) AS total_projects_bc,
            IFNULL(SUM(IF (p.person_mc_id = '.$user_id. ', 1, 0)), 0) AS total_projects_mc,
            IFNULL(SUM(IF (p.person_pl_id = '.$user_id. ', 1, 0)), 0) AS total_projects_pl,
            IFNULL(SUM(IF (readers.read_sections = 20, 1, 0)), 0) AS total_projects_reader ';

        $sql .= ' FROM ' . $this->_table . ' p ';

        $sql .= ' LEFT OUTER JOIN (SELECT s.project_id, COUNT(sr.reader_id) AS read_sections
                    FROM sections s
                    LEFT OUTER JOIN section_readers sr ON (sr.section_id = s.id)
                    WHERE sr.reader_id = '.$user_id. ' OR sr.reader_id IS NULL
                    GROUP BY s.project_id) AS readers ON (readers.project_id = p.id) ';

        // as other volunteer
        $sql .= ' WHERE (p.person_bc_id = '.$user_id. ' 
                OR p.person_altbc_id = '.$user_id. ' 
                OR p.person_mc_id = '.$user_id. '
                OR p.person_pl_id = '.$user_id. ')';


        if ($status == 'inactive')
        {
            $sql .= 'AND p.status = "complete" ';
        }    
        else
        {
            $sql .= 'AND p.status NOT IN ("complete", "abandoned", "on_hold") ';
        }    
                                          
        $query = $this->db->query($sql);
        return $query->row();


    }

    function get_project_ids_by_author($search)
    {
        $sql = 'SELECT pa.project_id
        FROM project_authors pa 
        JOIN authors a ON (a.id = pa.author_id)
        WHERE (a.first_name LIKE "%'. $search.'%" OR a.last_name LIKE "%'. $search.'%") 
        AND pa.type = "author"';
        
        $query = $this->db->query($sql);
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

    function get_project_readers($project_id)
    {
        $sql = 'SELECT DISTINCT u.id AS reader_id, u.display_name
                FROM users u
                JOIN section_readers sr ON (sr.reader_id = u.id) 
                JOIN sections s ON (s.id = sr.section_id) 
                WHERE s.project_id = ? ';

        $query = $this->db->query($sql, array($project_id));
        
        return $query->result_array();        
    }


    function create_project_reader_list($project_id)
    {
        $sql = 'SELECT DISTINCT u.display_name
        FROM users u
        JOIN section_readers sr ON (sr.reader_id = u.id) 
        JOIN sections s ON (s.id = sr.section_id) 
        WHERE s.project_id = ? ';

        $query = $this->db->query($sql, array($project_id));

        $reader_list = array();

        if ($query->num_rows())
        {
            foreach ($query->result() as $reader)
            {
                $reader_list[] = $reader->display_name;
            }   
        }

        return implode('; ', $reader_list);

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


        $sql = '
                SELECT DISTINCT "project" AS primary_type, p.id AS primary_key, p.id, p.project_type, p.title_prefix, p.title, 
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
                    $sql .=  ' AND l.id = ' . $params['primary_key'] ;
                }   
                if ($params['sub_category'] == 'genre' && $params['primary_key'])
                {
                    $sql .=  ' AND pg.genre_id = ' . $params['primary_key'] ;
                } 

                if ($params['author_id'])
                {
                    $sql .=  ' AND pa.author_id = ' . $params['author_id'] ;
                }      

                $sql .= $where_project_type;
                

        $sql .= '
                UNION
                SELECT DISTINCT "section" AS primary_type, s.id AS primary_key, p.id, p.project_type, "", CONCAT(s.title, " (in ", COALESCE(p.title_prefix, "" ), " ", p.title, " )") as title, 
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
                    $sql .=  ' AND (l.id = ' . $params['primary_key'] . ' OR s.language_id =  ' . $params['primary_key'] . ' )' ;
                } 
                if ($params['sub_category'] == 'genre' && $params['primary_key'])
                {
                    $sql .=  ' AND pg.genre_id = ' . $params['primary_key'] ;
                }                                 
                if ($params['author_id'])
                {
                    $sql .=    ' AND s.author_id = ' . $params['author_id'];
                }    

                $sql .= $where_project_type; 

                //skip empty sections this way
                $sql .= ' AND p.status = "' . PROJECT_STATUS_COMPLETE . '"';   

        $order = ($params['search_order'] == 'catalog_date') ? ' 7 DESC' : ' 6 ASC '; //release date, else alpha as default       

        $sql .= ' ORDER BY ' . $order ;      

        $sql .= ' LIMIT ' . $params['offset'] . ', ' . $params['limit']; 

        $query = $this->db->query($sql);
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
                SELECT DISTINCT "project" AS primary_type, p.id AS primary_key, p.id, p.project_type, p.title_prefix, p.title, 
                COALESCE(p.date_catalog, "2001-01-01" ), p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, l.language, p.url_forum

                FROM projects p

                JOIN languages l ON (l.id = p.language_id) ';

                $sql .= ' WHERE p.status = "' . PROJECT_STATUS_COMPLETE . '"

                ';

                $sql .= $where_project_type;                
               

        $order = ($params['search_order'] == 'catalog_date') ? ' 7 DESC' : ' 6 ASC '; //release date, else alpha as default       

        $sql .= ' ORDER BY ' . $order ;      

        $sql .= ' LIMIT ' . $params['offset'] . ', ' . $params['limit']; 

        $query = $this->db->query($sql); 

        //echo $this->db->last_query();               

        return $query->result_array();
    }





    function get_projects_by_reader($params)
    {

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

        $sql = 'SELECT DISTINCT p.id, p.project_type, p.title_prefix, p.title, p.date_catalog, p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, 
                    l.two_letter_code, l.language , p.url_forum
                FROM projects p 
                JOIN project_readers pr ON (pr.project_id = p.id) 
                JOIN languages l ON (l.id = p.language_id)               
                WHERE pr.reader_id = ' . $params['reader_id'] ;

        $sql .= $where_project_type;        

        $order = ($params['search_order'] == 'catalog_date') ? ' 5 DESC ' : ' 4 ASC '; //release date, else alpha as default       

        $sql .= ' ORDER BY ' . $order; 

        $sql .= ' LIMIT ' . $params['offset'] . ', ' . $params['limit'];        

        $query = $this->db->query($sql);                

        return $query->result_array();
    }





    function get_projects_by_group($params)
    {

        $sql = 'SELECT DISTINCT p.id, p.project_type, p.title_prefix, p.title, p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, 
                     l.two_letter_code, l.language, p.url_forum
                FROM projects p 
                JOIN group_projects gp ON (gp.project_id = p.id) 
                JOIN languages l ON (l.id = p.language_id)             
                WHERE gp.group_id = ? ' ;
                
        $sql .= ' ORDER BY p.title ASC ';

        $sql .= ' LIMIT ' . $params['offset'] . ', ' . $params['limit'];                


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
    		//$this->db->like(array('title'=>$where['projectname']));
            $where_clause = $this->_search_all_terms('title', $where['projectname']);
            $this->db->where($where_clause);
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


    function _search_all_terms($field, $string)
    {
        $query_string = '';
        $words = explode(" ", $string); 
        for($i=0; $i<count($words); $i++)
        {
            $query_string .= $field. " LIKE '%".$words[$i]."%' AND ";
        } 
        return $query_string = substr($query_string,0,strlen($query_string)-5); //poor man's kill the last "AND"
    }


    public function get_authors_by_project($project_id, $type= 'author')
    {
        $sql = '
        SELECT a.* 
        FROM project_authors pa 
        JOIN authors a ON (pa.author_id = a.id)
        WHERE pa.project_id = ? 
        AND pa.type = ? ';

        $query = $this->db->query($sql, array($project_id, $type));

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
                WHERE pr.reader_id = ' . $params['user_id'] . 
                $status_where .                 
                'ORDER BY p.title ASC ';
                //LIMIT '. $params['search_offset']. ',' . $params['search_limit'];

        $query = $this->db->query($sql);                

        return $query->result_array();
    }

    // for catalog author page
    /* prolly garbage now */
    function get_projects_by_author_status($params)
    {
        $status_where = ($params['status'] == 'complete') ? ' AND p.status = "complete" ': ' AND p.status != "complete" ';
        $sql = '
                SELECT p.id, p.project_type, p.title_prefix, p.title, p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, l.two_letter_code 
                FROM projects p 
                JOIN project_authors pa ON (pa.project_id = p.id) 
                JOIN languages l ON (l.id = p.language_id)
                WHERE pa.author_id = ' . $params['author_id'] . 
                $status_where;                 

        $sql .= '
                UNION
                SELECT p.id, p.project_type, p.title_prefix, CONCAT(s.title, " (in ", p.title, " )") as title, p.url_librivox, p.status, p.coverart_thumbnail, p.zip_url, p.zip_size, l.two_letter_code 
                FROM sections s 
                JOIN authors a ON (a.id = s.author_id)
                JOIN projects p ON (p.id = s.project_id)
                JOIN languages l ON (l.id = p.language_id)
                WHERE s.author_id = ' . $params['author_id'] . ' ' .
                $status_where . '
                AND p.is_compilation = 1 '; 

        $sql .= 'ORDER BY 4 ASC ';        


        $query = $this->db->query($sql);                

        return $query->result_array();
    }
}
        