<?php

class MY_Form_Validation extends CI_Form_Validation
{
	function alpha_dash_space($str)
    {
        return ( ! preg_match("/^([a-z0-9_ ])+$/i", $str)) ? FALSE : TRUE;
    }  

}