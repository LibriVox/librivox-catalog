<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Section_model extends MY_Model {

    function get_max_section_number($project_id)
    {
    	return $this->db->select('MAX(section_number) AS max_section_number')->where('project_id', $project_id)->get($this->_table)->row()->max_section_number;

    }

    function get_full_sections_info($project_id)
    {
        $result = $this->db->where(array('project_id'=>$project_id))
        ->order_by('section_number', 'asc')
        ->get($this->_table);

        $sections = $result->result();
        if ($result->num_rows()> 0)
        {
            foreach ($sections as $key => $section) {
                $sections[$key]->readers = $this->_get_section_reader($section->id);
                $sections[$key]->time   = gmdate("H:i:s", $section->playtime);

            }
        }  
        return $sections;      
    }

    function _get_section_reader($section_id)
    {
        return $this->db->select('u.username AS reader_name, u.display_name, u.id AS reader_id')
        ->join('users u', 'u.id = section_readers.reader_id', 'left outer')
        ->where('section_readers.section_id', $section_id)
        ->get('section_readers')->result();
    }

    function get_by_filename($project_id, $filename)
    {
        return $this->db->select($this->_table.'.*, p.title AS project_name, CONCAT(a.first_name, " ", a.last_name) AS author_name', false)
        ->join('projects p', 'p.id = '.$this->_table.'.project_id')
        ->join('authors a', 'a.id = '.$this->_table.'.author_id', 'left outer')
        ->where(array('project_id'=>$project_id))
        ->like('listen_url', $filename, 'before')
        ->get($this->_table)
        ->row();

    }

    function get_by_section_number($project_id, $section_number)
    {
        return $this->db->select($this->_table.'.*, p.title AS project_name, CONCAT(a.first_name, " ", a.last_name) AS author_name', false)
        ->join('projects p', 'p.id = '.$this->_table.'.project_id')
        ->join('authors a', 'a.id = '.$this->_table.'.author_id', 'left outer')
        ->where(array('project_id'=>$project_id))
        ->where(array('section_number'=>$section_number))
        ->get($this->_table)
        ->row();

    }

    function add_section_reader($project_id, $section_number, $reader_id)
    {
        //note section_number, not section id
        $section = $this->db->where(array('project_id'=>$project_id, 'section_number'=>$section_number))->get($this->_table)->row();
        if (empty($section)) return false;

        //add_section_reader
        $this->load->model('section_readers_model');
        $exists = $this->section_readers_model->count_by(array('section_id'=>$section->id, 'reader_id'=>$reader_id));

        if (!$exists)
        {
            $this->db->insert('section_readers', array('section_id'=>$section->id, 'reader_id'=>$reader_id));            
        }

    }

    function remove_section_reader($project_id, $section_number, $reader_id)
    {
        //note section_number, not section id
        $section = $this->db->where(array('project_id'=>$project_id, 'section_number'=>$section_number))->get($this->_table)->row();
        if (empty($section)) return false;

        $this->db->delete('section_readers', array('section_id'=>$section->id, 'reader_id'=>$reader_id));
        return $section->id;
        
    }

    function check_section_readers($project_id)
    {
        $sql = 'SELECT count(*) AS reader_count
                FROM sections s 
                LEFT OUTER JOIN section_readers sr ON (s.id = sr.section_id)
                WHERE s.project_id = ? 
                AND sr.section_id IS NULL';
        $query = $this->db->query($sql, array($project_id));
        return $query->row()->reader_count;
    }

    function get_sections_by_reader($reader_id, $role = 'reader')
    {
        $where = ($role == 'reader') ? ' sr.reader_id ' : ' p.person_pl_id ';

        $sql = 'SELECT p.id, p.title_prefix, p.title, p.status, s.section_number, s.status AS section_status, p.url_forum
                FROM section_readers sr  
                JOIN sections s ON (s.id = sr.section_id)
                JOIN projects p ON (p.id = s.project_id)
                WHERE ' .$where . ' = ? 
                AND p.status != ? 
                AND s.status IN ("Assigned", "Ready for PL", "See PL notes", "Ready for spot PL", "PL OK") 
                ORDER BY FIELD(s.status, "Assigned", "Ready for PL", "See PL notes", "Ready for spot PL", "PL OK" )';
        $query = $this->db->query($sql, array($reader_id, PROJECT_STATUS_COMPLETE));
        return $query->result();

    }

    function update_iarchive_urls($project_id, $iarchive_url)
    {

        // http://www.archive.org/download/archiveuploadname/filename_64kb.mp3
        // http://www.archive.org/download/archiveuploadname/filename_128kb.mp3

        //remove .mp3 ext from file_name

        $sql = 'UPDATE sections s 
                SET s.mp3_64_url = CONCAT("' . $iarchive_url . '", REPLACE(s.file_name, "_128kb.mp3", "" ) , "_64kb.mp3"),
                    s.mp3_128_url = CONCAT("' . $iarchive_url . '", REPLACE(s.file_name, "_128kb.mp3", "" ) , "_128kb.mp3")
                WHERE s.project_id = ? 
                AND s.file_name IS NOT NULL';
        $query = $this->db->query($sql, array($project_id));

        return true;

    }

    function update_section_sizes($project_id)
    {

        // length of recording (in seconds) times the bitrate (in this case 64 kbps) divided by 8 = size of file in kilobytes
        // size in kilobytes divided by 1024 = size in MB

        // so for a 20 min file at 64 kbps
        // [(1200 sec X 64kbps)/8 bits/byte]/1024 kb/MB = 9.3 MB

        // s.playtime * 64 / 8 / 1024


        $sql = 'UPDATE sections s
                SET s.mp3_64_size = ROUND((s.playtime * 64 / 8 / 1024), 1),
                    s.mp3_128_size = ROUND((s.playtime * 128 / 8 / 1024), 1)
                WHERE s.project_id = ?  ';

        $query = $this->db->query($sql, array($project_id));

        return true;
    }

    function get_total_project_runtime($project_id)
    {
        $sql = 'SELECT SUM(playtime) AS runtime FROM sections WHERE project_id = ? ';
        $query = $this->db->query($sql, array($project_id));

        return $query->row()->runtime;

    }

    function get_total_zipsize($project_id)
    {
        $sql = 'SELECT ROUND(SUM(REPLACE(mp3_64_size, "MB", "")), 0) AS zipsize FROM sections WHERE project_id = ? ';
        $query = $this->db->query($sql, array($project_id));

        return $query->row()->zipsize;        
    }

    function get_count_by_status($project_id, $status=array())
    {
        $status = sprintf('"%s"', implode('", "', $status));

        $sql = 'SELECT SUM(`count`) AS `count`
                FROM section_status_count s
                WHERE s.project_id = ?
                AND s.status IN ('.$status.');
        ';

        $query = $this->db->query($sql, array($project_id));

        return $query->row()->count; 

    }

    function truncate_temp_section_status_count()
    {
        $sql = 'TRUNCATE TABLE `section_status_count`';
        $query = $this->db->query($sql);

        return true;

    }

    function create_temp_section_status_count()
    {

        $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `section_status_count` (
                  `project_id` int(11) NOT NULL,
                  `status` varchar(55) NOT NULL,
                  `count` int(11) NOT NULL
                );";

        $query = $this->db->query($sql);

        return true;
    }

    function populate_section_status_count()
    {
        $sql = "INSERT INTO section_status_count (project_id, status, count)
                SELECT s.project_id, s.status, COUNT(*) AS count
                FROM sections s
                GROUP BY s.project_id, s.status";
        $query = $this->db->query($sql);

        return true;
    } 
}

/* End of file section_model.php */
/* Location: ./application/models/section_model.php */
