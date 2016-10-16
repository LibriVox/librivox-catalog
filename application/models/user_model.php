<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model {

    public function __construct()
    {
       parent::__construct();
    }

    function get_users($params=array())
    {
        //$limit = 'LIMIT 0, 500';
        $limit = '';

        $sql = 'SELECT DISTINCT u.* 
        FROM ' . $this->_table . ' u
        LEFT OUTER JOIN users_groups ug ON (ug.user_id = u.id)
        LEFT OUTER JOIN roles r ON (ug.group_id = r.id)
        WHERE u.id NOT IN (1,2) AND u.active = 1 '; //set these to constants

        if (!empty($params['user_search']))
        {
            $sql .= ' AND (u.username LIKE "%' . $params['user_search'] . '%" OR u.email LIKE "%' . $params['user_search'] . '%") ' ;
        }   

        if (!empty($params['user_type']))
        {   
            if ($params['user_type'] == 'all') 
                $limit = '';
            else  
                $sql .= ' AND r.name = "'. $params['user_type'] .'" ';
        } 

        $sql .= ' ORDER BY u.username '. $limit;

        $query = $this->db->query($sql);
        return $query->result();
    }

    function get_user_by_role($role)
    {
        $sql = 'SELECT * 
        FROM users u 
        JOIN users_groups ug ON (ug.user_id = u.id)
        JOIN roles r ON (ug.group_id = r.id)
        WHERE r.name = ? 
        ORDER BY u.username ASC';

        $query = $this->db->query($sql, array($role));
        return $query->result();

    }

    function get_dropdown_by_role($role, $add_empty=true, $use_id = true)
    {
        $result = $this->get_user_by_role($role);

        $dropdown = array();

        if ($add_empty) $dropdown[''] = '- - Select '.$role.' - - -';

        foreach ($result as $user)
        {
            $key = ($use_id)? $user->user_id: strtolower(str_replace(' ', '_', $user->username));
            $dropdown[$key] = $user->username;
        }    

        unset($dropdown['administrator']);unset($dropdown[1]);

        return $dropdown;

    }

    public function search_by($term, $search_field)
    {
        $query = $this->db->select('*')->order_by($search_field)->like($search_field, $term)->get($this->_table);
        return $query->result();
    }



}

/* End of file person_model.php */
/* Location: ./application/models/person_model.php */
