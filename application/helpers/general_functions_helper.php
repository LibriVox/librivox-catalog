<?php


function is_empty($str, $empty = null)
{
    return (empty($str))? $empty: $str;
}

function is_empty_object($str, $field ,$empty = null)
{
    return (empty($str))? $empty: $str->$field;
}

function safe_divide($numerator, $divisor, $default=0)
{
    if (empty($divisor)) return $default;

    return $numerator/$divisor;
}

function get_host($url='')
{
    $url = (empty($url))? base_url(): $url;
    //return base url without http so we can add subdomain to front
    $url_array = parse_url($url);
    return $url_array['host']. '/';  // add trailing slash so it behaves like Codeigniter's base_url() function
 }

function replace_accents($str) {
   $str = htmlentities($str, ENT_COMPAT, "UTF-8");
   $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/','$1',$str);
   return html_entity_decode($str);
}


function build_view_path($method)
{
	if (strpos($method, '::'))
	{
		$method = str_replace('::','/',$method);
	}
	return strtolower($method);
}

function country_list()
{
    return array(
        ''   => 'Select',
        'US' => 'United States');
}


function months()
{
    return array( 0 => 'Month',
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December');
}

function days()
{
    $array = array();
    for ($i=1; $i<=31; $i++) {
        $array[$i] = $i;
    }
    return $array;
}

function years()
{
    $array = array();
    for ($i=date('Y'); $i<=date('Y')+5; $i++) {
        $array[$i] = $i;
    }
    return $array;
}

function hours()
{
    //$array = array('' => 'HH');
    for ($i=0; $i<=23; $i++) {
        $array[$i] = sprintf('%02d', $i);
    }
    return $array;
}
function minutes()
{
    //$array = array('' => 'MM');
    for ($i=0; $i<=59; $i++) {
        $array[$i] = sprintf('%02d', $i);
    }
    return $array;
}
function seconds()
{
    //$array = array('' => 'SS');
    for ($i=0; $i<=59; $i++) {
        $array[$i] = sprintf('%02d', $i);
    }
    return $array;
}

function alphabet()
{
    return range('a', 'z');
}


function full_languages_dropdown($id='', $selected_id=''  )
{
    $ci = &get_instance();

    $ci->load->model('language_model');
    $common_languages = $ci->language_model->order_by('language', 'asc')->get_many_by(array('common'=>1));     
    $languages = $ci->language_model->order_by('language', 'asc')->dropdown('id', 'language');   //as_array()->get_all();

    $html = '<select id="'.$id.'" name="'.$id.'">';

    if (!empty($common_languages))
    {
        foreach ($common_languages as $key => $common_language) {
            $selected = ($common_language->id == $selected_id)? ' selected ': '';
            $html .= '<option value="'. $common_language->id .'" '. $selected .'  >'. $common_language->language .'</option>';
        }
        //separator
        $selected = ($selected_id == '')? ' selected ': '';
        $html .= '<option value="" '. $selected .' > ------ </option>';
    }

    if (!empty($languages))
    {
        foreach ($languages as $key => $language) {
            $selected = ($key == $selected_id)? ' selected ': '';
            $html .= '<option value="'. $key .'" '. $selected .'>'. $language .'</option>';
        }
    }

    $html .= '</select>';

    return $html;            

}


function time_string_to_secs($time_string)
{
    $time_array = explode(':', $time_string);


    // we need to do this because times are not consistently entered in
    // 00:00:00 format (might only be 00:00)
    switch (count($time_array))
    {
        case 1: 
            $hours  = 0;
            $mins   = 0;
            $secs   = $time_array[0];   
            break;
        case 2: 
            $hours  = 0;
            $mins   = $time_array[0];
            $secs   = $time_array[1];   
            break;
        case 3: 
            $hours  = $time_array[0];
            $mins   = $time_array[1];
            $secs   = $time_array[2];  
            break;
        default:
            $hours  = 0;
            $mins   = 0;
            $secs   = 0;                
    }

    return ($hours * 3600 ) + ($mins * 60 ) + $secs;
     
}
