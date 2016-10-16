<div class="tpl">

	<div style="height:100px;">
		<div class="pull-left" >
			<h1><?= $page_title?></h1>
			<h2><?= lang('project_launch_template_copy_from_here')?></h2>
		</div>
		<div class="pull-right">
			<?= $forum_link; ?>
			<?= $help_link; ?>
		</div>
	</div>
	<hr />
  
<textarea id="copy_content" style="width:1200px;height:600px;">
<?php 
	$author_all = (count($authorall) == 1) ? $authorall[0]: $authorall[0]. ' and others';

	$translator_all = '';
	if (!empty($translatorall) && ($translatorall[0] !=''))
	{
		$translator_all = (count($translatorall) == 1) ?  $translatorall[0]: $translatorall[0]. ' and others';
		$translator_all = lang('project_launch_template_trans_by') . ' ' . $translator_all;
	}
	
?>
<?= sprintf(lang('project_launch_template_author_line'),$title, $author_all, $translator_all   )?>


<?php if(!empty($notice)): foreach ($notice as $key => $notice_msg): ?>
	<?php if (!empty($notice_msg)): ?>
	<?= $notice_msg ?>
	<?php endif; ?>
<?php endforeach;endif;?>

[quote] <?= $brief_summary ?><?= $summaryauthor ?>  [/quote]

 [list]
	<?php if (!empty($date)): ?>
	   [*][b]<?= lang('project_launch_template_target_completion_date')?>[/b]  <?= $date ?>
	<?php endif;?> 


<?php // This area different for project type ?>

[list=1][*][b]<?= lang('project_launch_template_how_to_claim_part')?>[/b] <?= lang('project_launch_template_how_to_claim_part_2')?>

[*][b]<?= lang('project_launch_template_new_to_recording')?>[/b] <?= lang('project_launch_template_please_read_guide')?>

[*][b]<?= lang('project_launch_template_is_there_deadline')?>[/b] <?= lang('project_launch_template_is_there_deadline_answer')?>
[color=red][b]<?= lang('project_launch_template_is_there_deadline_answer_2')?>[/b][/color]

[*][b]<?= lang('project_launch_template_where_is_text')?>[/b] <?= lang('project_launch_template_where_is_text_answer')?> <?= $link_to_text ?>  

[*][b]<?= lang('project_launch_template_claim_sections')?>
 [i]<?= lang('project_launch_template_what_name')?>[/i][/b]

<?= lang('project_launch_template_proof_listeners')?>  <?= $proof_level ?> 
<? //end different area ?>


[color=red][b]<?= lang('project_launch_template_please_dont_download')?>[/b][/color]
<? //= $downloads ?>



[MW]xxxx[/MW]

===========================================  
<?= lang('project_launch_template_temp_paragraph')?> 

 [list] 
 [*] Project Code: <?= $project_code ?> 
<?php if(!empty($link_to_auth)):foreach($link_to_auth as $key=>$link_to_auth_single):?>
	<?php $link_to_auth_single =  (empty($link_to_auth_single))? 'n/a': $link_to_auth_single ; ?>
	<?php $link_to_auth_single = '(' . $author[$key]. ') : ' . $link_to_auth_single; ?>

	 [*]<?= lang('project_launch_template_author_wiki')?> <?= $link_to_auth_single ?>  
<?php endforeach;endif; ?>
 [*]<?= lang('project_launch_template_title_wiki')?> <?= $link_to_book ?>  
 [*]<?= lang('project_launch_template_num_sections')?> <?= $num_sections ?>  
 [*]<?= lang('project_launch_template_has_preface')?> <?= $has_preface ?>  
 [*]<?= lang('project_launch_template_orig_pub_date')?> <?= $pub_year ?>  
 [*]<?= lang('project_launch_template_name_credit')?> <?= $soloist_name ?>
 <?= lang('project_launch_template_self_url')?> <?= $soloist_link ?>  [/list]
============================================ 

<?= lang('project_launch_template_genres')?>
 <?= $genres ?> 

<?= lang('project_launch_template_keywords')?>
 <?= $keyword ?> 
 
============================================ 
 
[*][b]<?= lang('project_launch_template_before_recording')?>[/b] [b][i]<?= lang('project_launch_template_check_notes')?>[/b][/i] http://librivox.org/forum/viewtopic.php?p=6427#6430

[b][i]<?= lang('project_launch_template_set_recording_software')?>[/b][/i]
<?= lang('project_launch_template_channels')?>: 1 (Mono)
<?= lang('project_launch_template_bit_rate')?>: 128 kbps
<?= lang('project_launch_template_sample_rate')?>: 44.1 kHz


[*][b]<?= lang('project_launch_template_middle_of_recording')?>:[/b] 
[b][i]<?= lang('project_launch_template_seconds_of_silence')?>[/i][/b]
[b][i]<?= lang('project_launch_template_add_to_beginning')?>[/b][/i]
[b]<?= lang('project_launch_template_start_of_recording')?>[/b][list][*][i]<?= sprintf(lang('project_launch_template_chapter_number'), $title)?>[/i]
[*]<?= lang('project_launch_template_if_you_wish')?>
[i] <?= lang('project_launch_template_recording_by')?>[/i]
 [*] <?= lang('project_launch_template_say')?>  
[i] <?= sprintf(lang('project_launch_template_author_intro'),$title, $author[0], $translator_all  )?> [/i] [/list] 
[b]<?= lang('project_launch_template_end_of_recording')?> [/b][list][*]<?= lang('project_launch_template_end_of_section')?>  
[i]<?= lang('project_launch_template_end_of_chapter')?>[/i] 
[*]<?= lang('project_launch_template_if_you_wish')?>  
[i]<?= lang('project_launch_template_recording_by')?> [/i] 
[*]<?= lang('project_launch_template_end_of_book')?>   
[i]<?= sprintf(lang('project_launch_template_end_of_title'),$title, $author[0], $translator_all  )?> [/i][/list] 
[b][i]<?= lang('project_launch_template_end_silence')?>[/b][/i]

<?= lang('project_launch_template_check_for_updates')?>

[*][b]<?= lang('project_launch_template_after_recording')?>[/b] 
[b]<?= lang('project_launch_template_need_noise_cleaning')?>[/b]
<?= lang('project_launch_template_noise_cleaning_software')?>
 
[b]<?= lang('project_launch_template_save_files_as')?>[/b] 
128 kbps MP3 
 <?= $url ?> 

[*][b]<?= lang('project_launch_template_example_tags')?>[/b]  
<?= lang('project_launch_template_id3_tags_learn_more')?> 
<?= lang('project_launch_template_id3_tags_add')?>

 <?= lang('project_launch_template_artist')?>  <?= $author[0] ?>

 <?= lang('project_launch_template_title')?> ## - [Section title]
 <?= lang('project_launch_template_album')?>  <?= $title ?> 

<?= lang('project_launch_template_tags_ignore')?>

<?= lang('project_launch_template_transfer_of_files')?>
 [color=blue][i][b]<?= lang('project_launch_template_please_post')?>
 <?= lang('project_launch_template_please_post_length')?>[/b][/i][/color]
 [list][*]<?= lang('project_launch_template_upload_with_uploader')?>
 <?= base_url(UPLOADER_LINK) ?> 
 [img]<?= base_url(). $project_img_url?>[/img] 
 <?= lang('project_launch_template_if_you_have_trouble')?>
 [*][color=blue]<?= lang('project_launch_template_select_mc')?> yy - yyyyy[/color]
 [*][color=#FF0000][b]<?= lang('project_launch_template_please_post_link')?>[/b][/color]
 [*]<?= lang('project_launch_template_please_check_send_recording')?>[/list]


[b]<?= lang('project_launch_template_any_questions')?>[/b] 
<?= lang('project_launch_template_post_below')?>[/list]
 
</textarea>
<hr />
<div style="height:100px;">
	<div class="pull-right">
		<?= $forum_link; ?>
	</div>
</div>
