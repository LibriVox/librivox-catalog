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



<?= lang('project_launch_template_each_fortnight_poem_chosen')?>

<?= sprintf(lang('project_launch_template_this_fortnight_poem'), $link_to_text)?>

 [*] Project Code: <?= $project_code ?> 

[b]<?= lang('project_launch_template_set_recording_software')?>[/b] 
<?= lang('project_launch_template_channels')?>: 1 (Mono)
<?= lang('project_launch_template_bit_rate')?>: 128 kbps 
<?= lang('project_launch_template_sample_rate')?>: 44100 kHz

[b]<?= lang('project_launch_template_questions_how')?>[/b] 
<?= lang('project_launch_template_check_recording_notes')?>
<?= lang('project_launch_template_useful_link')?>

[b]<?= lang('project_launch_template_begin_with_disclaimer')?>[/b]
<?= lang('project_launch_template_seconds_of_silence')?>
[quote] <?= sprintf(lang('project_launch_template_title_author_read_by'), $title, $author[0])?> 
<?= lang('project_launch_template_title_author_read_by_include')?>[/quote]

[b]<?= lang('project_launch_template_read_poem')?>[/b]

[quote] 
<?= lang('project_launch_template_insert_poem_text')?> 
[/quote] 

[b]<?= lang('project_launch_template_at_end_of_reading')?>[/b] 
[quote]<?= lang('project_launch_template_end_of_poem')?>[/quote] 
<?= lang('project_launch_template_leave_five_seconds')?>

<?= lang('project_launch_template_save_recording')?> 
[b]<?= lang('project_launch_template_file_name')?>[/b] <?= lang('project_launch_template_lower_case')?> [b][color=indigo] <?= $url ?> [/color][/b] 

[b]<?= lang('project_launch_template_id3_tags')?>[/b] 
<?= lang('project_launch_template_artist_name')?> <?= $author[0] ?> 
<?= lang('project_launch_template_track_title')?>  <?= sprintf(lang('project_launch_template_read_by_poem'), $title, $title)?>

<?= lang('project_launch_template_album_title')?> <?= lang('project_launch_template_librivox_fortnightly_poem')?> 
<?= lang('project_launch_template_comments')?> <?= lang('project_launch_template_optional_recorded_by')?>


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


<?= lang('project_launch_template_include_credit_name')?>

[b][color=red]<?= sprintf(lang('project_launch_template_contribute_deadline'), $date) ?> [/color][/b] 

<?= $downloads ?>


[MW]xxxx[/MW]

<?= lang('project_launch_template_suggest_poem_fortnightly')?>

</textarea>
<hr />
<div style="height:100px;">
	<div class="pull-right">
		<?= $forum_link; ?>
	</div>
</div>