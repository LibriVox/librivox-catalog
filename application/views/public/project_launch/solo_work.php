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
 [*][b]<?= lang('project_launch_template_text_source')?> [/b] <?= $link_to_text ?> 
 [*][b]<?= lang('project_launch_template_type_of_proof')?> [/b] <?= $proof_level ?>  


<?php // This area different for project type ?>

<?= lang('project_launch_template_soloist_note')?>
<? //end different area ?>

[color=red][b]<?= lang('project_launch_template_please_dont_download')?>[/b][/color]
<?php //= $downloads ?>



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

[*][b][i]<?= lang('project_launch_template_reader_will_record')?> [/i][/b]
<?= lang('project_launch_template_seconds_of_silence')?>

[b]<?= lang('project_launch_template_start_of_recording')?>:[/b]
[list][*][i]<?= sprintf(lang('project_launch_template_chapter_number'), $title)?>[/i]
[*] <?= lang('project_launch_template_if_you_wish')?> 
 <?= lang('project_launch_template_recording_by')?>
 [*] <?= lang('project_launch_template_say')?> 
 [i]<?= sprintf(lang('project_launch_template_author_intro'),$title, $author[0], $translator_all )?> [/i][/list] 

<?= lang('project_launch_template_shortened_intro')?>
[list][*][i]<?= sprintf(lang('project_launch_template_public_domain'),$title, $author[0], $translator_all )?> [/i] 
[*]<?= lang('project_launch_template_if_you_wish')?> 
 <?= lang('project_launch_template_recording_by')?> 
 [*] <?= lang('project_launch_template_if_applicable')?> 
 [i]<?= lang('project_launch_template_chapter_title')?>[/i][/list]

[b]<?= lang('project_launch_template_end_of_recording')?>:[/b]
[list][*]<?= lang('project_launch_template_end_of_section')?> 
 [i]<?= lang('project_launch_template_end_of_chapter')?>[/i] 
 [*]<?= lang('project_launch_template_if_you_wish')?> 
 [i]<?= lang('project_launch_template_recording_by')?>[/i]

[*]<?= lang('project_launch_template_end_of_book')?> 
 [i]<?= sprintf(lang('project_launch_template_end_of_title'),$title, $author[0], $translator_all )?> [/i][/list] 
 [b][i]<?= lang('project_launch_template_end_silence')?> [/i][/b]

[*][b]<?= lang('project_launch_template_example_filename')?> [/b]   <?= $url ?> 

[*]<?= lang('project_launch_template_example_tags')?> 
 <?= lang('project_launch_template_artist')?>  <?= $author[0] ?>

 <?= lang('project_launch_template_title')?> ## - [Section title] 
 <?= lang('project_launch_template_album')?>  <?= $title ?> 


[b]<?= lang('project_launch_template_transfer_of_files')?>[/b]
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