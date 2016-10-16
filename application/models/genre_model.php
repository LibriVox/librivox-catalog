<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Genre_model extends MY_Model {

	//helps up convert a list OR array of ids and return a list of the names
    function convert_ids_to_name($ids)
    {
    	if (empty($ids)) return '';

    	if (is_string($ids)) $ids = explode(',', $ids);

        foreach ($ids as $key => $id) {
            $genres[] = $this->get_full_name_string($id);
        }

/*
    	$query= $this->db->select('name')
    	->where_in('id', $ids)
    	->get($this->_table);

    	$genres = array();
    	if ($query->num_rows())
    	{
    		foreach ($query->result() as $key => $value) {
    			//$genres[] = $value->name;
                var_dump($value);
                $genres[] = $this->get_full_name_string($value->id);
    		}
    	}	
*/
    	return implode('; ', $genres);
    }

    // given a genre id, will create a full path from top-most ancestor down to that item
    function get_full_name_string($id, $separator ='/')
    {
        $config['table'] = 'genres';
        $this->load->library('mahana_hierarchy', $config);        

        $ancestors = $this->mahana_hierarchy->get_ancestors($id);
        foreach ($ancestors as $key => $value) {
            $items[] = $value['name'];
        }        

        return implode($separator, $items);

    }

}

/* End of file genre_model.php */
/* Location: ./application/models/genre_model.php */
