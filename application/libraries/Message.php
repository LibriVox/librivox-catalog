<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* Message:: a library for giving feedback to the user
*
* @author  Adam Jackett
* @url http://www.darkhousemedia.com/
* @version 1.2.0
*/

class CI_Message {
	
	public $CI;
	public $wrapper = array('', '');


	public function __construct($config=null){    
		$this->CI =& get_instance();        
		$this->CI->load->library('session');
		
		if(is_array($config)) $this->initialize($config);

		$this->dialog_type = 'close';
	}

	public function initialize($config){
		if(!is_array($config)) return false;
		
		foreach($config as $key => $val){
			$this->$key = $val;
		}
	}
	
	public function set($type, $message, $group='default'){
		if(!is_array($message)) $message = array($message);
		foreach($message as $msg){
			$obj = new stdClass();
			$obj->message = $msg;
			$obj->type = $type;
			$obj->group = $group;
		}

		$flash_messages = array();	
		if(isset($this->CI->session->userdata['flash:new:_messages'])){
			$flash_message = $this->CI->session->userdata['flash:new:_messages'];
		}
		$flash_messages[] = $obj;
		if(count($flash_messages)) $this->CI->session->set_flashdata('_messages', $flash_messages);
	}
	
	public function display($group='default', $wrapper=FALSE){
		echo $this->get($group, $wrapper);
	}
	
	public function get($group='default', $wrapper=FALSE){
		$content = '';
		
		$this->messages = array();

		if(isset($this->CI->session->userdata['flash:new:_messages'])){
			$this->messages = $this->CI->session->userdata['flash:new:_messages'];
		} 
		if(isset($this->CI->session->userdata['flash:old:_messages'])){
			$this->messages = array_merge($this->CI->session->userdata['flash:old:_messages'] , $this->messages );
		}
			unset($this->CI->session->userdata['flash:old:_messages']);
			unset($this->CI->session->userdata['flash:new:_messages']);
			$this->CI->session->sess_write();	

		if(count($this->messages)){
			$output = array();
			foreach($this->messages as $msg){
				if($msg->group == $group){
					if(!isset($output[$msg->type])) $output[$msg->type] = array();
					$output[$msg->type][] = $msg->message;
				}
			}
			$content .= ($wrapper !== FALSE ? $wrapper[0] : $this->wrapper[0])."\r\n";

			/*
			foreach($output as $type => $messages){
				$content .= '<div class="message message-'.$type.'">'."\r\n";
				foreach($messages as $msg){
					$content .= '<p>'.$msg.'</p>'."\r\n";
				}
				$content .= '</div>'."\r\n";
			}
			*/

			foreach($output as $type => $messages){
				$content .= '<div class="'.$type.'">'."\r\n";
				$content .= '<button type="button" class="close" data-dismiss="alert">×</button>'."\r\n";
				foreach($messages as $msg){
					$content .= '<p>'.$msg.'</p>'."\r\n";
				}
				$content .= '</div>'."\r\n";
			}

			$content .= ($wrapper !== FALSE ? $wrapper[1] : $this->wrapper[1])."\r\n";
		}
		return $content;
	}

	public function validation_errors(){
		if(!function_exists('validation_errors')) $this->CI->load->helper('form');

		$temp_errors = explode("\n", strip_tags(validation_errors()));
		$errors = array();
		foreach($temp_errors as $e){
			if(!empty($e)) $errors[] = $e;
		}
		return $errors;
	}

	public function keep(){
		$this->CI->session->keep_flashdata('_messages');
	}


} 
