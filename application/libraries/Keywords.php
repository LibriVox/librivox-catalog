<?php defined('BASEPATH') or exit('No direct script access allowed');


/**
 * Keywords Library
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS
 * @subpackage	Libraries
 * @category	Keywords
 */

/*
	Modified for Librivox by Jeff Madsen (www.codebyjeff.com)
*/

class Keywords {

	protected $ci;

	/**
	 * The Keywords Construct
	 */
	public function __construct()
	{
		$this->ci =& get_instance();

		$this->ci->load->model('keyword_model', 'keyword_m');
	}

	/**
	 * Get keywords
	 *
	 * Gets all the keywords
	 *
	 * @param	string	$hash	The unique hash stored for a entry
	 * @return	array
	 */
	public function get_string($list_keywords)
	{
		$keywords = array();
		
		foreach ($this->ci->keyword_m->get_applied($list_keywords) as $keyword)
		{
			$keywords[] = $keyword->value;
		}
		
		return implode(', ', $keywords);
	}
	
	/**
	 * Get keywords
	 *
	 * Gets all the keywords as an array
	 *
	 * @param	string	$hash	The unique hash stored for a entry
	 * @return	array
	 */
	public function get_array($hash)
	{
		$keywords = array();
		
		foreach ($this->ci->keyword_m->get_applied($hash) as $keyword)
		{
			$keywords[] = $keyword->name;
		}
		
		return $keywords;
	}
	
	/**
	 * Get keywords as links
	 *
	 * Returns keyword list as processed links
	 *
	 * @param	string	$hash	The unique hash stored for a entry
	 * @return	array
	 */
	public function get_links($hash, $path = '')
	{
		$keywords = $this->ci->keyword_m->get_applied($hash);
		$i = 1;
		
		if (is_array($keywords))
		{
			$links = '';

			foreach ($keywords as $keyword)
			{
				$links .= anchor(trim($path, '/').'/'.str_replace(' ', '-', $keyword->name), $keyword->name) . ($i < count($keywords) ? ', ' : '');
				$i++;
			}
			return $links;
		}
		
		return $keywords;
	}

	/**
	 * Add Keyword
	 *
	 * Adds a new keyword to the database
	 *
	 * @param	array	$keyword
	 * @return	int
	 */
	public function add($keyword)
	{
		return $this->ci->keyword_m->insert(array('value' => self::prep($keyword)));
	}

	/**
	 * Prepare Keyword
	 *
	 * Gets a keyword ready to be saved
	 *
	 * @param	string	$keyword
	 * @return	bool
	 */
	public function prep($keyword)
	{
		return strtolower(trim($keyword));
	}

	/**
	 * Process Keywords
	 *
	 * Process a posted list of keywords into the db
	 *
	 * @param	string	$group	Arbitrary string to "namespace" unique requests
	 * @param	string	$keywords	String containing unprocessed list of keywords
	 * @return	string
	 */
	public function process($keywords)
	{	
		// No keywords? Let's not bother then
		if ( ! ($keywords = trim($keywords)))
		{
			return '';
		}
		
		// Split em up and prep away
		$keywords = explode(',', $keywords);
		foreach ($keywords as &$keyword)
		{
			$keyword = self::prep($keyword);
			// Keyword already exists
			if (($row = $this->ci->db->where('value', $keyword)->get('keywords')->row()))
			{
				$keyword_id[] = $row->id;
			}
			
			// Create it, and keep the record
			else
			{
				$keyword_id[] = self::add($keyword);
			}
			
		}
		
		return implode(',', $keyword_id);
	}

}

/* End of file Keywords.php */
