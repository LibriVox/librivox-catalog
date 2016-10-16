<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Number_list_parser{
	


	public function __construct()
	{

	}

	public function __get($var)
	{
		return get_instance()->$var;
	}	

	public function parse_list($chapters)
	{


		//just a single number passed
		if (is_numeric($chapters)) return array((int)$chapters);

		$chapter_array = array();

		//break up based on , 
		$comma_sets = explode(',', $chapters);

		//look at each & see if is range or no
		foreach ($comma_sets as $key => $comma_set) {
			if (strpos($comma_set, '-') === FALSE)
			{
				$chapter_array[] = (int)$comma_set;
			} 
			else
			{
				list($first, $last) = explode('-', $comma_set);
				$chapter_array = array_merge($chapter_array, range($first, $last));
			} 
		}

		return $chapter_array;

	}
}	