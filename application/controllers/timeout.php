<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Timeout extends CI_Controller {

#	public function index()
#	{
#		$i = 0;
#		while (1) {
#			sleep(2);
#			$i++;
#			echo $i. '<br />';
#		}
#
#
#
#	}

	public function index() {
		$timeout = 60;
		sleep($timeout);
		echo "Slept for $timeout seconds.";
	}

}

/* End of file timeout.php */
/* Location: ./application/controllers/timeout.php */
