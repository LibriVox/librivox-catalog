<div class="row">
	<div class="pull-left">
		<h1><?= $page_title?></h1>
		<h2><?= lang('project_launch_template_copy_from_here')?></h2>
	</div>
	<div class="pull-right">
		<?= $forum_link; ?>
		<?= $help_link; ?>
	</div>
</div>
<div class="row">
	<button id="copy" class="btn btn-primary pull-right"><?= lang('project_launch_template_copy_button')?></button>
</div>
<div class="row">
<textarea id="textarea" class="span12" style="margin-top:10px;resize:none;opacity:0">
<?php
	$upload_msg = function() use ($project_type, $project_img_url)
	{
		echo "\n";
		echo lang('project_launch_template_upload_with_uploader'), ' ', base_url(UPLOADER_LINK), "\n";

		if ($project_type != 'solo')
		{
			echo '[img]' . base_url() . "${project_img_url}[/img]\n";
			echo lang('project_launch_template_if_you_have_trouble'), "\n";
		}

		echo "\n";
		echo '[b]', lang('project_launch_template_select_mc'), "[/b] [color=#0000FF]xxxx[/color]\n";
		echo "\n";

		if ($project_type == 'solo')
			echo lang('project_launch_template_copy_and_paste_solo'), "\n";
		else
			echo lang('project_launch_template_copy_and_paste_group'), "\n";

		if ($project_type == 'poetry_weekly' || $project_type == 'poetry_fortnightly')
		{
			echo "\n";
			echo lang('project_launch_template_include_credit_name'), "\n";
		}
	};

	$author_text = $author[0];
	$author_all_text = $authorall[0];
	if (count($author) > 1)
	{
		$author_text .= ' and others';
		$author_all_text .= ' and others';
	}
	if ($translator[0])
	{
		$author_text .= ', ' . lang('project_launch_template_trans_by') . ' ' . $translator[0];
		$author_all_text  .= ', ' . lang('project_launch_template_trans_by') . ' ' . $translatorall[0];
		if (count($translator) > 1)
		{
			$author_text .= ' and others';
			$author_all_text .= ' and others';
		}
	}

	echo '[color=indigo][size=150][b]', sprintf(lang('project_launch_template_title_author'), $title, $author_all_text), "[/b][/size][/color]\n";

	foreach ($notice as $notice_msg)
	{
		if (!empty($notice_msg))
		{
			echo "\n";
			echo "[color=red]${notice_msg}[/color]\n";
		}
	}

	echo "\n";
	echo "[quote]$brief_summary ${summaryauthor}[/quote]\n";

	if ($project_type == 'solo' || $project_type == 'collaborative' || $project_type == 'dramatic')
	{
		echo "\n";
		echo lang('project_launch_template_text_source'), " $link_to_text\n";
		echo "\n";
		if ($project_type == 'solo')
			echo '[b]', lang('project_launch_template_target_completion_date'), "[/b] $date\n";
		else
			echo lang('project_launch_template_deadline'), "\n";

		if ($project_type == 'collaborative')
		{
			echo "\n";
			echo lang('project_launch_template_claiming_sections'), "\n";
		}
		else if($project_type == 'dramatic')
		{
			echo "\n";
			echo lang('project_launch_template_claiming_roles'), "\n";
			echo lang('project_launch_template_note_public_domain'), "\n";
		}

		if ($project_type != 'solo')
		{
			echo "\n";
			echo lang('project_launch_template_new_to_recording'), "\n";
		}

		echo "\n";
		echo '[b]', lang('project_launch_template_proof_level'), '[/b] ';
		echo lang('proj_launch_proof_level_' . $proof_level), "\n";
		echo lang('project_launch_template_prospective_pls'), "\n";

		if ($project_type == 'solo') {
			echo "\n";
			echo lang('project_launch_template_soloist_note'), "\n";
		}
	}
	else if ($project_type == 'poetry_weekly' || $project_type == 'poetry_fortnightly')
	{
		echo "\n";

		if ($project_type == 'poetry_weekly')
		{
			echo lang('project_launch_template_each_week_poem_chosen'), "\n";
			echo sprintf(lang('project_launch_template_this_week_poem'), $link_to_text), "\n";
		}
		else
		{
			echo lang('project_launch_template_each_fortnight_poem_chosen'), "\n";
			echo sprintf(lang('project_launch_template_this_fortnight_poem'), $link_to_text), "\n";
		}

		echo "\n";
		echo lang('project_launch_template_new_to_recording'), "\n";
		echo "\n";
		echo lang('project_launch_template_recording_settings'), "\n";
		echo "\n";
		echo lang('project_launch_template_begin_with_disclaimer'), "\n";
		echo lang('project_launch_template_seconds_of_silence'), "\n";
		echo '[quote]', sprintf(lang('project_launch_template_title_author_read_by'), $title, $author[0]), "\n";
		echo lang('project_launch_template_title_author_read_by_include'), "[/quote]\n";
		echo "\n";
		echo '[b]', lang('project_launch_template_read_poem'), "[/b]\n";
		echo "\n";
		echo "[quote]\n";
		echo lang('project_launch_template_insert_poem_text'), "\n";
		echo "[/quote]\n";
		echo "\n";
		echo '[b]', lang('project_launch_template_at_end_of_reading'), "[/b]\n";
		echo '[quote]', lang('project_launch_template_end_of_poem'), "[/quote]\n";
		echo lang('project_launch_template_end_silence'), "\n";
		echo "\n";
		echo '[b]', lang('project_launch_template_filename'), "[/b] $url\n";
		$upload_msg();
		echo "\n";
		echo lang('project_launch_template_check_back'), "\n";
		echo "\n";
		echo lang('project_launch_template_deadline_poetry'), "\n";
	}

	echo "\n";
	echo '[color=red]', lang('project_launch_template_please_dont_download'), "[/color]\n";
	echo "\n";
	echo "[MW]xxxx[/MW]\n";
	if ($project_type == 'poetry_weekly' || $project_type == 'poetry_fortnightly')
	{
		echo "Project Code: $project_code\n";
	}
	else
	{
		echo str_repeat('=', 40), "\n";
		echo lang('project_launch_template_temp_paragraph'), "\n";
		echo "\n";
		echo "[list]\n";
		echo "[*]Project Code: $project_code\n";
		$link_to_auth_count = 0;
		foreach ($link_to_auth as $key => $link_to_auth_single)
		{
			if (!empty($link_to_auth_single))
			{
				echo '[*]', lang('project_launch_template_author_wiki');
				echo " $link_to_auth_single ($author[$key])\n";
				$link_to_auth_count++;
			}
		}
		if (!$link_to_auth_count)
			echo '[*]', lang('project_launch_template_author_wiki'), "\n";
		echo '[*]', lang('project_launch_template_title_wiki'), " $link_to_book\n";
		echo '[*]', lang('project_launch_template_num_sections'), " $num_sections\n";
		echo '[*]', lang('project_launch_template_has_preface'), " $has_preface\n";
		echo '[*]', lang('project_launch_template_orig_pub_date'), " $pub_year\n";
		echo '[*]', lang('project_launch_template_name_credit'), " $soloist_name\n";
		echo '[*]', lang('project_launch_template_self_url'), " $soloist_link\n";
		echo "[/list]\n", str_repeat('=', 40), "\n";
		echo "\n";
		echo lang('project_launch_template_genres'), " $genres\n";
		echo "\n";
		echo lang('project_launch_template_keywords'), " $keyword\n";
		echo "\n";
		echo str_repeat('=', 40), "\n";
		echo "\n";
		echo lang('project_launch_template_recording_settings'), "\n";
	}

	if ($project_type == 'solo' || $project_type == 'collaborative')
	{
		echo "\n";
		echo '[b]', lang('project_launch_template_start_of_recording'), "[/b]\n";
		echo lang('project_launch_template_seconds_of_silence'), "\n";
		echo "\n";
		echo ($project_type == 'solo') ? lang('project_launch_template_for_first_say') : lang('project_launch_template_say'), "\n";
		echo '[quote]', sprintf(lang('project_launch_template_section_of_first'), $title,
			lang('project_launch_template_this_is_a_librivox_recording'), $title,
			$author_text), "[/quote]\n";

		if ($project_type == 'solo')
		{
			echo "\n";
			echo lang('project_launch_template_shortened_intro'), "\n";
			echo '[quote]', sprintf(lang('project_launch_template_section_of_second'), $title, $author_text), "[/quote]\n";
		}

		echo "\n";
		echo '[b]', lang('project_launch_template_end_of_recording'), "[/b]\n";
		echo lang('project_launch_template_say'), "\n";
		echo '[quote]', lang('project_launch_template_end_of_section'), "[/quote]\n";
		echo "\n";
		echo lang('project_launch_template_if_final_section'), "\n";
		echo '[quote]', sprintf(lang('project_launch_template_end_of_title'), $title, $author_text), "[/quote]\n";
		echo "\n";
		echo lang('project_launch_template_end_silence'), "\n";
	}
	else if ($project_type == 'dramatic')
	{
		echo "\n";
		echo '[b]', lang('project_launch_template_for_individual_roles'), "[/b]\n";
		echo lang('project_launch_template_one_file_per_act'), "\n";
		echo '[quote]', lang('project_launch_template_character_read_by'), "[/quote]\n";
		echo "\n";
		echo lang('project_launch_template_space_between_lines'), "\n";
		echo "\n";
		echo '[b]', lang('project_launch_template_for_narration'), "[/b]\n";
		echo lang('project_launch_template_seconds_of_silence'), "\n";
		echo "\n";
		echo lang('project_launch_template_say'), "\n";
		echo '[quote]', sprintf(lang('project_launch_template_act_of'), $title, $author_text,
			lang('project_launch_template_this_is_a_librivox_recording')), "[/quote]\n";
		echo "\n";
		echo lang('project_launch_template_end_of_file'), "\n";
		echo '[quote]', lang('project_launch_template_end_of_act'), "[/quote]\n";
		echo "\n";
		echo lang('project_launch_template_if_final_section'), "\n";
		echo '[quote]', sprintf(lang('project_launch_template_end_of_title'), $title, $author_text), "[/quote]\n";
		echo "\n";
		echo lang('project_launch_template_end_silence'), "\n";
	}

	if (!($project_type == 'poetry_weekly' || $project_type == 'poetry_fortnightly'))
	{
		echo "\n";
		echo '[b]', lang('project_launch_template_filename'), '[/b]';
		if ($project_type == 'dramatic')
		{
			echo "\n", lang('project_launch_template_for_individual_roles'), " $url[0]\n";
			echo lang('project_launch_template_for_final_files'), " $url[1]\n";
		}
		else
		{
			echo " $url\n";
		}

		$upload_msg();
	}

	if ($project_type == 'poetry_weekly')
	{
		echo "\n";
		echo lang('project_launch_template_suggest_poem_weekly'), ' ';
		echo lang('project_launch_template_suggest_poem_link'), "\n";
	}
	else if ($project_type == 'poetry_fortnightly')
	{
		echo "\n";
		echo lang('project_launch_template_suggest_poem_fortnightly'), ' ';
		echo lang('project_launch_template_suggest_poem_link'), "\n";
	}

	if ($project_type != 'solo')
	{
		echo "\n";
		echo lang('project_launch_template_any_questions'), "\n";
	}
?>
</textarea>
</div>
