<?php

// functions specific to setting up strings for the Result Page

function format_filename($string)
{
	// Convert to all ASCII characters (which also removes accents), lower case and drop all but alpha numberic.
	// This will even convert strings such as "汉语" to "hanyu" ("han yu" before spaces are removed).
	$transliterator = Transliterator::create('Any-Latin;Latin-ASCII;Lower;[^a-z0-9] Remove');
	return $transliterator->transliterate($string);
}

function author_name($fields)
{
	$fields['dob'] = (empty($fields['dob'])) ? '' : $fields['dob'];
	return $fields['first_name']. " " .$fields['last_name']. " (" . $fields['dob'] . " - " . $fields['dod'] . ")";
}

function clean_title($title, $retval = 'title')
{

	$ci = & get_instance();
	$title_remove = $ci->config->item('title_remove');

	$prefix = '';
	$first_space = strpos($title, ' ', 1);
	$first_word = substr($title, 0, $first_space);
	if (in_array($first_word, $title_remove))
	{
		$prefix = $first_word;
		$title = substr($title, $first_space +1);
	}

	if ($retval == 'title') return $title;

	return $prefix;
}

function create_full_title($project)
{
	if (is_array($project)) $project = (object) $project;

	$project->full_title = (!empty($project->title_prefix))? $project->title_prefix.' ': '';
    return $project->full_title .= $project->title;
}

function summary_author($fields)
{
	return '(' . lang('project_launch_template_summary_by') . ' ' . $fields['brief_summary_by'] . ')';
}

function concat_date($year, $month, $day)
{
	$month = date( 'm', mktime(0, 0, 0, $month) );
	$day = str_pad($day, 2, 0, STR_PAD_LEFT);
	return $year . "-" . $month . "-" . $day;
}

function clean_summary($project_launch_data)
{
	return $project_launch_data->brief_summary . $summary = (empty($project_launch_data->brief_summary_by)) ? '' :' - Summary by '. $project_launch_data->brief_summary_by;
}

function copyright_notice($fields)
{
	//Copyright notices for death plus 50/70 countries
    $thisyear = date('Y');
    $copyauthor = $thisyear - $fields['dod'];
    $author = $fields['first_name']. " " .$fields['last_name'];

	if ($copyauthor < 69) {
		$notice = sprintf(lang('project_launch_template_copyright_warning'), $author, $fields['dod']);
	} else {
		$notice = null;
	}
	return $notice;
}

function create_array_from_lang($check_string, $lang, $alpha = false)
{
	$check_len = strlen($check_string);
	$values = array();

	foreach ($lang as $key => $value)
	{
		if (substr($key, 0, $check_len) == $check_string)
			$values[substr($key, $check_len)] = $value;
	}

	if ($alpha)
	{
		//asort($values);
		array_multisort(array_map('sortify', $values), $values);
	}

	return $values;
}

function sortify($string)
{
    return preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1' . chr(255) . '$2', htmlentities($string, ENT_QUOTES, 'UTF-8'));
}


function build_genre_element($genre)
{
	$ci = & get_instance();
	$ci->load->library('mahana_hierarchy');

	$ancestors = $ci->mahana_hierarchy->get_ancestors($genre);

	foreach ($ancestors as $value) {
		$name[] = $value['name'];
	}

	$full_name = implode('/', $name);

	//should make a view
	return '<span class="tag genre_tag" style="cursor:pointer;" data-id="'.$genre.'" data-name="'.$full_name.'"><span>'.$full_name.'&nbsp;&nbsp;</span><a class="remove_genre_item" title="Removing tag">x</a></span>';

}

function create_title_slug($project)
{
	//https://librivox.org/the-count-of-monte-cristo-by-alexandre-dumas/
	$project = (object) $project;

	if (!empty($project->url_librivox))
	{
		$slug = str_replace('https://librivox.org/', '', $project->url_librivox);
	}

	if (empty($slug))
	{
		return $project->id;
	}

	return $slug;

}


// builds link to catalog author's page
function build_author_link($author, $wiki='', $link='')
{

	if (empty($author)) return '';

	$link = (empty($link))? base_url().'author/'. $author->id : $link;

	return '<a href="'.$link.'">'. $wiki . build_author_name($author) .'</a>';
}

function build_author_name($author)
{
	return implode(' ', array_filter(array($author->first_name, $author->last_name)));
}

function get_language_code($language)
{
	if (empty($language)) return '';

	return (empty($language->two_letter_code)) ? $language->three_letter_code: $language->two_letter_code;

}

function build_author_years($author)
{
	$author->dob = (empty($author->dob)) ? ' ': $author->dob;
	$author->dod = (empty($author->dod)) ? ' ': $author->dod;

	return $author->dob === ' ' && $author->dod === ' '
		? ""
		: sprintf("(%s - %s)", $author->dob, $author->dod);
}

function format_playtime($seconds)
{
	$remainder = $seconds;
	$seconds   = $remainder % 60;
	$remainder = ($remainder - $seconds) / 60;
	$minutes   = $remainder % 60;
	$hours     = ($remainder - $minutes) / 60;
	return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}
