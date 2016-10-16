<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Upload extends CI_Upload
{

	/**
	 * Set the file name
	 *
	 * This function takes a filename/path as input and looks for the
	 * existence of a file with the same name. 
	 *
	 *
	 * EXTENSION:
	 * If found, it will append a date to the OLD file to avoid overwriting a pre-existing file.
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function set_filename($path, $filename)
	{
		if ($this->encrypt_name == TRUE)
		{
			mt_srand();
			$filename = md5(uniqid(mt_rand())).$this->file_ext;
		}

		if ( ! file_exists($path.$filename))
		{
			return $filename;
		}

		$original = $filename;
		$filename = str_replace($this->file_ext, '', $filename);

		$date = date('Y_m_d_H_i_s');
		$new_filename = $filename.$date.$this->file_ext;

		$this->rename_existing_file($path, $original, $new_filename);

		return $original;
	}

	public function rename_existing_file($path, $original, $new_filename)
	{
		return rename($path.$original, $path.$new_filename);
	}

}