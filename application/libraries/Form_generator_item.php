<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class Form_generator_item {

	public $ci;

	private $author_list = array();

	private $new_author_list = array();

	private $trans_list = array();

	private $new_trans_list = array();	

	public function __construct($config=null){    
		$this->ci =& get_instance();    

		$this->ci->load->model('form_generators_authors_model');    
		$this->ci->load->model('form_generator_model');

	}

	//recieves entire $_POST
	function alter_data($fields)
	{
		//clean data 

		//---$fields['genres'] = $this->_process_genres($fields['genres']);

		//authors
		if (isset($fields['auth_id']))
		{		
			$this->_process_authors('auth', $fields);

			$fields['author_list'] 		= implode(',', $this->author_list);
			$fields['new_author_list'] 	= implode(',',$this->new_author_list);

			//we want a good clean way to clean up unwanted elements of the fields array as we work. for now we will just do them "manually" as we go
			unset($fields['auth_id'],$fields['auth_first_name'],$fields['auth_last_name'],$fields['auth_yob'],$fields['auth_yod'], $fields['link_to_auth']);			
		}

		//translators
		if (isset($fields['trans_id']))
		{
			$this->_process_authors('trans', $fields);

			$fields['trans_list'] 		= implode(',', $this->trans_list);
			$fields['new_trans_list'] 	= implode(',',$this->new_trans_list);

			//we want a good clean way to clean up unwanted elements of the fields array as we work. for now we will just do them "manually" as we go
			unset($fields['trans_id'],$fields['trans_first_name'],$fields['trans_last_name'],$fields['trans_yob'],$fields['trans_yod'], $fields['link_to_trans']);
		}

		$fields['list_keywords'] = $this->_process_keywords( $fields['list_keywords']);

		


		////insert - (no updating for now, but that could change)
		$this->ci->form_generator_model->insert($fields);

		return $fields;
	}

	private function _process_keywords( $keywords_tag)	
	{
		$this->ci->load->library('keywords'); 
		return $this->ci->keywords->process($keywords_tag);

	}


	function _process_genres($genres)
	{
		return (empty($genres))? '' :implode(',', $genres);

	}

	function _process_authors($type, &$fields)
	{

		$author_array = $this->_set_author_array($type ,$fields);

		if (empty($author_array))
		{
			return array();
		}	

		$author_list = array();
		$new_author_list = array();

		$array_keys = array_keys($author_array['auth_id']);

		foreach ($array_keys as $i) {

			//new or existing author?
			if ($author_array['auth_id'][$i])
			{
				$author_list[] = $author_array['auth_id'][$i];
			}	
			else
			{
				//insert record & add to new_author_list
				$author['auth_id'] 			= $author_array['auth_id'][$i];
				$author['auth_first_name'] 	= $author_array['auth_first_name'][$i];
				$author['auth_last_name'] 	= $author_array['auth_last_name'][$i];
				$author['auth_yob']	 		= $author_array['auth_yob'][$i];
				$author['auth_yod']	 		= $author_array['auth_yod'][$i];
				$author['link_to_auth']	 	= $author_array['link_to_auth'][$i];

				if (!empty($author['auth_first_name']) || !empty($author['auth_last_name']))
				{
					$insert_id = $this->_add_new_author($author);
					$new_author_list[] = $insert_id;					
				}	

			}	

		}

		//pass outside
		$this->_set_list_array($type, $author_list);
		$this->_set_new_list_array($type, $new_author_list);

	}

	function _set_author_array($type, &$fields)
	{
		$author_array['auth_id'] 			= $fields[$type.'_id'];
		$author_array['auth_first_name'] 	= $fields[$type.'_first_name'];
		$author_array['auth_last_name'] 	= $fields[$type.'_last_name'];
		$author_array['auth_yob']	 		= $fields[$type.'_yob'];
		$author_array['auth_yod']	 		= $fields[$type.'_yod'];
		$author_array['link_to_auth']	 	= $fields['link_to_'.$type];

		return $author_array;
	}

	function _set_list_array($type, $list)
	{
		if ($type == 'trans')
		{
			$this->trans_list = $list;
		}	
		else
		{			
			$this->author_list = $list;
		}	

	}

	function _set_new_list_array($type, $list)
	{
		if ($type == 'trans')
		{
			$this->new_trans_list = $list;
		}	
		else
		{			
			$this->new_author_list = $list;
		}	

	}

	function _add_new_author($author)
	{
		return $this->ci->form_generators_authors_model->insert($author);
	}

}	