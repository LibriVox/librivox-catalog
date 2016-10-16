<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// codeigniter wrapper for the getid3 library http://www.getid3.org/

class getid3_wrapper
{
	public function __construct()
	{
		require_once('getid3/getid3.php');
	}

	public function write_tag($filename, $tag_data)
	{
		require_once('getid3/write.php');
		require_once('getid3/write.id3v1.php');

		$tagwriter = new getid3_writetags;

		$tagwriter->filename = trim($filename);

		if (!is_file($filename)) return 'No file found';

		$tagwriter->tagformats = array('id3v2.3');
		$tagwriter->overwrite_tags = true;
		$tagwriter->tag_encoding   = 'UTF-8';
		$tagwriter->remove_other_tags = false;

		$tagwriter->tag_data = $tag_data;

		$message = '';

		// remove old ID3v2 tags
        if ($tagwriter->DeleteTags(array('id3v1'))) {
            if (!empty($tagwriter->warnings)) {
                $message .= 'There were some warnings:<br>'.implode('<br><br>', $tagwriter->warnings);
            }
        } else {
            $message .= 'Failed to remove ID3v1 tags!<br>'.implode('<br><br>', $tagwriter->errors);
        }

		// write ID3v2 tags		
		if ($tagwriter->WriteTags()) 
		{
		    $message .= 'Successfully wrote tags<br>';
		    if (!empty($tagwriter->warnings)) 
		    {
		        $message .= 'There were some warnings:<br>'.implode('<br><br>', $tagwriter->warnings);
		    }
		} 
		else 
		{
		    $message .= 'Failed to write tags!<br>'.implode('<br><br>', $tagwriter->errors);
		}
		return $message;
	}
}		