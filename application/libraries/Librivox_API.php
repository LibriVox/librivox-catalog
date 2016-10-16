<?php defined('BASEPATH') OR exit('No direct script access allowed');

// our api is quite simple, so we'll just build it all here
class Librivox_API{
	
	//private $ci = &get_instance();


	public function __construct()
	{
		//Do your magic here

		$this->load->model('project_model');
	}

	public function __get($var)
	{
		return get_instance()->$var;
	}	

	public function get_audiobooks($params)
	{

		//temp data

		//notes - format function will make "authors" => "author"

		$array =  $this->_build_data_set($params);  //array('book'=>$this->project_model->get_by('id',5055));

		return $array;

	}

	public function get_audiotracks($params)
	{
		if (!empty($params['id'])) $array['sections'] =  $this->_get_section($params['id']);
		elseif (!empty($params['project_id'])) $array['sections'] =  $this->_get_sections($params['project_id']); 
		else $array = false;

		return $array;

	}	

	public function get_authors($params)
	{
		if (!empty($params['id'])) $array['authors'] =  $this->_get_author($params);
		elseif (!empty($params['last_name'])) $array['authors'] =  $this->_get_author($params); 
		else $array['authors'] =  $this->_get_authors(0, 'author');

		return $array;

	}		

	function _build_data_set($params)
	{
		/*
		$params['offset'] 	= $this->get('offset'); -- checked
		$params['limit'] 	= $this->get('limit');	-- checked
		$params['id'] 		= $this->get('id');     -- checked

		$params['since'] 	= $this->get('since');  -- checked

		$params['genre'] 	= $this->get('genre');  -- checked
		$params['title'] 	= $this->get('title');
		$params['author'] 	= $this->get('author');  -- checked

		$params['extended'] = $this->get('extended');
		
		*/

		//var_dump($params);
		//return $params;

		$extended = (empty($params['extended'])) ? 0 : 1;
		
		$limit = (!empty($params['limit'])) ? $params['limit'] : 50;
		$offset = (!empty($params['offset'])) ? $params['offset'] : 0;

		/*

			*** Searching ***

		*/

		if ($params['id']) $this->db->where('p.id', $params['id']);

		if ($params['since']) $this->db->where('UNIX_TIMESTAMP(STR_TO_DATE(p.date_catalog, "%Y-%m-%d")) >=  ', $params['since']);

		// we can do title directly against projects table
		if ($params['title'])
		{
			if (substr($params['title'], 0, 1) == '^')
			{
				$params['title'] = substr($params['title'], 1);
				$this->db->like('p.title', $params['title'],'after');
			}	
			else
			{
				$this->db->where('p.title', $params['title']);
			}			
		} 


		if (!empty($params['author']))
		{
			$project_id_list = $this->_build_author_project_id_list($params['author']);
			$this->db->where_in('p.id', $project_id_list);
		}

		if (!empty($params['genre']))
		{
			$project_id_list = $this->_build_genre_project_id_list($params['genre']);
			$this->db->where_in('p.id', $project_id_list);
		}


		/*  Build main query */

		$result =  $this->db->select('p.*, l.language', false)
			->join('languages l', 'p.language_id=l.id')
			->limit($limit, $offset)
			->get('projects p')
			->result_array();

		if (empty($result)) return false;

		foreach ($result as $key => $row) {
			$project['id'] 				= $row['id'];
			$project['title'] 			= $row['title'];
			$project['description'] 	= $row['description'];
			$project['url_text_source'] = $row['url_text_source'];
			$project['language'] 		= $row['language'];
			$project['copyright_year'] 	= $row['copyright_year'];
			$project['num_sections'] 	= $row['num_sections'];

			$project['url_rss'] 		= 'http://librivox.org/rss/'.$row['id'];
			//$project['url_zip_file'] 	= 'http://www.archive.org/download/'. $row['title'] . '_' . $row['id'] . '_librivox_/' . $row['title'] . '_' . $row['id'] . '_librivox_64kb_mp3.zip';
			$project['url_zip_file'] 	= $row['zip_url'];
			$project['url_project'] 	= $row['url_project'];
			$project['url_librivox'] 	= $row['url_librivox'];
			
			$project['url_other'] 		= $row['url_other'];

			$project['totaltime'] 		= $row['totaltime'];
			$project['totaltimesecs'] 	= time_string_to_secs($row['totaltime']);


			// get authors
			$project['authors']			= $this->_get_authors($row['id'], 'author');


			if ($extended)
			{
				$project['url_iarchive'] 	= $row['url_iarchive'];

				//get sections
				$project['sections']		= $this->_get_sections($row['id']);

				if (!empty($project['sections']))
				{
					foreach ($project['sections'] as $key=>$section)
					{
						$project['sections'][$key]['readers'] =$this->_get_readers($section['id']);
					}	
				}

				// get genres
				$project['genres']			= $this->_get_genres($row['id']);

				// get translators
				$project['translators']		= $this->_get_authors($row['id'], 'translator');

			}

			// Filter out the list so only fields param request is returned (we can move some stuff higher to avoid all the extra queries)	

			if (!empty($params['fields']))
			{
				if (is_array($params['fields']))
				{
					$filter = $params['fields'];
				}
				else
				{
					$filter = explode(",", str_replace(array("{", "}"), array("", ""), $params['fields']));
				}

				$project = array_intersect_key($project, array_flip($filter));
			}
			

			$book['books'][$key] = $project;
		}

		return $book;
	}

	function _get_authors($project_id, $type)
	{
		if ($project_id) $this->db->where('pa.project_id', $project_id);

		$result =  $this->db->select('DISTINCT a.id, a.first_name, a.last_name, a.dob, a.dod', false)
		->join('project_authors pa', 'pa.author_id = a.id')
		->where('pa.type', $type)
		->order_by('a.last_name', 'asc')
		->get('authors a')
		->result_array();

		return $result;  
	}

	function _get_author($params)
	{
		if ($params['id']) $this->db->where('a.id', $params['id']);

		if ($params['last_name']) $this->db->where('a.last_name', $params['last_name']);

		$result =  $this->db->select('a.id, a.first_name, a.last_name, a.dob, a.dod', false)
		->get('authors a')
		->result_array();

		return $result;  
	}

	function _get_sections($project_id)
	{
		$result =  $this->db->select('s.id, s.section_number, s.title, s.listen_url, COALESCE(l.language, "English") AS language, COALESCE(s.playtime, 0) AS playtime, s.file_name', false)
		->join('languages l', 'l.id=s.language_id', 'left outer')
		->where('s.project_id', $project_id)
		->get('sections s')
		->result_array();

		return $result; 
	}

	function _get_section($id)
	{
		$result =  $this->db->select('s.id, s.section_number, s.title, s.listen_url, COALESCE(l.language, "English") AS language, COALESCE(s.playtime, 0) AS playtime, s.file_name', false)
		->join('languages l', 'l.id=s.language_id', 'left outer')
		->where('s.id', $id)
		->get('sections s')
		->row_array();

		return $result; 
	}

	function _get_readers($section_id)
	{
		$result =  $this->db->select('u.id AS reader_id, u.display_name', false)
		->join('users u', 'u.id=sr.reader_id', 'left outer')
		->where('sr.section_id', $section_id)
		->get('section_readers sr')
		->result_array();

		return $result; 
	}


	function _get_genres($project_id)
	{
		$result =  $this->db->select('g.id, g.name', false)
		->join('project_genres pg', 'pg.genre_id = g.id')
		->where('pg.project_id', $project_id)
		->get('genres g')
		->result_array();

		return $result; 
	}	

	function _build_author_project_id_list($author)
	{
		if (substr($author, 0, 1) == '^')
		{
			$author = substr($author, 1);
			$this->db->like('a.last_name', $author,'before');
		}	
		else
		{
			$this->db->where('a.last_name', $author);
		}

		$result = $this->db->select('pa.project_id')
			->join('project_authors pa', 'pa.author_id = a.id')
			->where('pa.type', 'author')
			->get('authors a')
			->result_array();
			
		require_once(APPPATH.'libraries/underscore.php');
		
		$project_ids = __()->pluck($result , 'project_id');

		return (empty($project_ids)) ? 0 : $project_ids;
		//return implode(', ', $project_ids);
				
	}

	function _build_genre_project_id_list($genre)
	{
		if (substr($genre, 0, 1) == '^')
		{
			$genre = substr($genre, 1);
			$this->db->like('LOWER(g.name)', strtolower($genre),'after');
		}	
		else
		{
			$this->db->where('g.name', $genre);
		}

		$result = $this->db->select('pg.project_id')
			->join('project_genres pg', 'pg.genre_id = g.id')
			->get('genres g')
			->result_array();

		//return $result;
			
		require_once(APPPATH.'libraries/underscore.php');
		
		return $project_ids = __()->pluck($result , 'project_id');
		//return implode(', ', $project_ids);
				
	}

}