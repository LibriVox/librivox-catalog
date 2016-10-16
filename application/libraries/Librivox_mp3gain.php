<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Librivox_mp3gain
{

	private $flag; 

	private $mp3gain; 


	public function __construct($config=null)
	{

		$this->flag 	= ' -o  ';

		$this->mp3gain  = 'mp3gain';

		//just for my dev environment, til I reboot :-)
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			$this->mp3gain  = '"C:\Program Files\MP3Gain\mp3gain"';
		}

		

		//$this->_set_tests(); //this way we can override which tests we want to run

		if(is_array($config)) $this->initialize($config);
	}

	public function initialize($config){
		if(!is_array($config)) return false;
		
		foreach($config as $key => $val){
			$this->$key = $val;
		}
	}

	public function __get($var)
	{
		return get_instance()->$var;
	}		

	public function analyze($dir, $map)
	{
		if (empty($dir) || empty($map)) return false;

		$this->flag 	= ' -o  ';

		foreach ($map as $key=>$file_name)
		{
			$command = $this->mp3gain .  ' ' . $this->flag . $dir. $file_name;
			exec($command, $output);

			$parts = explode("\t", $output[1]); 

			unset($output);
		}	
	}


	public function analyze_file($dir, $file_name)
	{
		if (empty($file_name)) return false;

		$this->flag 	= ' -o  ';

		$command = $this->mp3gain .  ' ' . $this->flag . $dir. $file_name;
		exec($command, $output);

		$parts = explode("\t", $output[1]); 

		return $parts;

	}


	public function adjust($dir, $map)
	{
		
		if (empty($dir) || empty($map)) return false;

		$this->flag 	= ' -q -k -r ';

		foreach ($map as $key=>$file_name)
		{
			exec($this->mp3gain .  ' ' . $this->flag . $dir. $file_name, $output);

			unset($output);
		}			
	}		
}