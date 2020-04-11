<?php

class MY_Form_validation extends CI_Form_validation
{
	function alpha_dash_space($str)
    {
        return ( ! preg_match("/^([a-z0-9_ ])+$/i", $str)) ? FALSE : TRUE;
    }  

}
