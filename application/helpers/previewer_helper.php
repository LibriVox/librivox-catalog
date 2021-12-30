<?php

// functions specific to setting up strings for the Result Page

function format_filename($string)
{
	// Convert to all ASCII characters (which also removes accents), lower case and drop all but alpha numberic.
	// This will even convert strings such as "汉语" to "hanyu" ("han yu" before spaces are removed).
	$transliterator = Transliterator::create('Any-Latin;Latin-ASCII;Lower;[^a-z0-9] Remove');
	return $transliterator->transliterate($string);
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

// WARNING: The create_full_title function is also used by the WordPress theme
// (also see comment before format_authors).
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

function concat_date($year, $month, $day, $fmt = 'Y-m-d')
{
	if ($year == 0 || $month == 0 || $day == 0)
		return '';
	else
		return date($fmt, mktime(0, 0, 0, $month, $day, $year));
}

function clean_summary($project_launch_data)
{
	return $project_launch_data->brief_summary . $summary = (empty($project_launch_data->brief_summary_by)) ? '' :' - Summary by '. $project_launch_data->brief_summary_by;
}

function copyright_notice($fields)
{
	// Copyright notices for death plus 50/70 countries
    $now = date('Y');
	$dod = (int)$fields['dod'];

	if (($now - $dod) < 69)
		return sprintf(lang('project_launch_template_copyright_warning'), format_author($fields), $dod);
	else
		return null;
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

function get_language_code($language)
{
	if (empty($language)) return '';

	return (empty($language->two_letter_code)) ? $language->three_letter_code: $language->two_letter_code;

}

function format_author_name($author)
{
	if (isset($author->author))
		return trim($author->author);
	else
		return implode(' ', array_filter(array($author->first_name, $author->last_name)));
}

function format_author_years($author)
{
	$dob = empty($author->dob) ? '' : $author->dob;
	$dod = empty($author->dod) ? '' : $author->dod;

	if (!empty($dob) || !empty($dod))
		return '(' . $dob . ' - ' . $dod . ')';
	else
		return '';
}

define ('FMT_AUTH_YEARS', 0x01);
define ('FMT_AUTH_HTML',  0x02);
define ('FMT_AUTH_LINK',  0x04);
define ('FMT_AUTH_WIKI',  0x08);

// WARNING: the format authors function is also used (loaded via a require_once)
// by the WordPress theme codebase. Make sure if you change this function that
// you also double check it will not break the WordPress theme!
function format_authors($authors, $flags = 0, $max = 0)
{
	$flag_years = (bool)($flags & FMT_AUTH_YEARS);
	$flag_html  = (bool)($flags & FMT_AUTH_HTML);
	$flag_link  = (bool)($flags & FMT_AUTH_LINK);
	$flag_wiki  = (bool)($flags & FMT_AUTH_WIKI);

	$list = array();
	foreach ($authors as $author)
	{
		// Some callers pass author as an array instead of an object. Some callers
		// use "author_id" but most just use "id". The following block of code
		// normalizes theses differences.
		if (is_array($author))
		{
			if (isset($author['author_id']))
			{
				$author['id'] = $author['author_id'];
				unset($author['author_id']);
			}
			$author = (object)$author;
		}

		$item = format_author_name($author);

		if ($flag_years)
		{
			$years = format_author_years($author);
			if (!empty($years))
			{
				if ($flag_html)
					$item .= ' <span class="dod-dob">' . $years . '</span>';
				else
					$item .= ' ' . $years;
			}
		}

		if ($flag_wiki)
			$item = '<a href="' . $author->author_url . '">Wiki - ' . $item . '</a>';
		else if ($flag_link)
			$item = '<a href="' . base_url() . 'author/' . $author->id . '">' . $item . '</a>';

		$list[] = $item;
	}

	$count = count($list);
	if ($max && $count > $max)
		return implode(', ', array_slice($list, 0, $max)) . ' et al.';
	else if ($count > 1)
	{
		$last = array_pop($list);
		return implode(', ', $list) . ' and ' . $last;
	}
	else if ($count)
		return $list[0];
	else
		return '';
}

function format_author($author, $flags = 0)
{
	if (isset($author))
		return format_authors(array($author), $flags);
	else
		return '';
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
