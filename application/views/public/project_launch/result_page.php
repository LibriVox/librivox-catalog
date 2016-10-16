<div class="tpl">
<h1><?= $page_title ?></h1>
<h2><?= lang('project_launch_template_copy_from_here')?></h2>

<hr />
  
<?= sprintf(lang('project_launch_template_author_line'),$title, $authorall, $translatorall   )?>

[quote] <?= $brief_summary ?><?= $brief_summary_by ?>  [/quote]<p>

<p><?= $notice ?><p>

<?= $page_intro ?>

<?= $downloads ?>

[MW]xxxx[/MW]<p>

=========================================== <br /> 
<?= lang('project_launch_template_temp_paragraph')?> <p>

 [list] 
<br /> [*]<?= lang('project_launch_template_author_wiki')?> <?= $link_to_auth ?>  
<br /> [*]<?= lang('project_launch_template_title_wiki')?> <?= $link_to_book ?>  
<br /> [*]<?= lang('project_launch_template_num_sections')?> <?= $num_sections ?>  
<br /> [*]<?= lang('project_launch_template_has_preface')?> <?= $has_preface ?>  
<br /> [*]<?= lang('project_launch_template_orig_pub_date')?> <?= $pub_year ?>  
<br /> [*]<?= lang('project_launch_template_name_credit')?> <?= $soloist_name ?>
<br /> <?= lang('project_launch_template_self_url')?> <?= $soloist_link ?>  [/list]<br />
============================================ <p>

<?= lang('project_launch_template_genres')?><br />
 <?= $genres ?> <p>

<?= lang('project_launch_template_keywords')?><br />
 <?= $keyword ?> <p>
 
 

 
 
 

[*]<?= lang('project_launch_template_reader_will_record')?> 
<br /><?= lang('project_launch_template_seconds_of_silence')?>

<p><?= lang('project_launch_template_start_of_recording')?>[list][*]<?= sprintf(lang('project_launch_template_chapter_number'), $title)?>[*] <?= lang('project_launch_template_if_you_wish')?> 
<br /> [*] <?= lang('project_launch_template_say')?> 
<br /> <?= sprintf(lang('project_launch_template_author_intro'),$title, $author, $translator )?> [/list] <p>

<?= lang('project_launch_template_shortened_intro')?>
[list][*]<?= sprintf(lang('project_launch_template_public_domain'),$title, $author, $translator )?>  
[*]<?= lang('project_launch_template_if_you_wish_2')?> 
<br /> <?= lang('project_launch_template_recording_by')?> 
<br /> [*] <?= lang('project_launch_template_if_applicable')?> 
<br /> <?= lang('project_launch_template_chapter_title')?>[/list]

<?= lang('project_launch_template_end_of_recording')?>[list][*]<?= lang('project_launch_template_end_of_section')?> 
<br /> <?= lang('project_launch_template_end_of_chapter')?> 
<br /> [*]<?= lang('project_launch_template_if_you_wish_2')?> 
<br /> <?= lang('project_launch_template_recording_by')?><p>

[*]<?= lang('project_launch_template_end_of_book')?> 
<br /> <?= sprintf(lang('project_launch_template_end_of_title'),$title, $author, $translator )?> [/list] 
<br /> <?= lang('project_launch_template_end_silence')?> <p>

[*]<?= lang('project_launch_template_example_filename')?>  <br />  <?= $url ?> <p>

[*]<?= lang('project_launch_template_example_tags')?> 
<br /> <?= lang('project_launch_template_title')?> ## - [Section title] 
<br /> <?= lang('project_launch_template_artist')?>  <?= $author ?>
<br /> <?= lang('project_launch_template_album')?>  <?= $title ?> <p>

<?= lang('project_launch_template_transfer_of_files')?>
<br /> [color=blue][i][b]<?= lang('project_launch_template_please_post')?>
<br /> <?= lang('project_launch_template_please_post_length')?>[/b][/i][/color]
<br /> [list][*]<?= lang('project_launch_template_upload_with_uploader')?>
<br /> http://upload.librivox.org 
<br /> [img]http://kayray.org/audiobooks/librivox/login.jpg[/img] 
<br /> <?= lang('project_launch_template_if_you_have_trouble')?>
<br /> [*][color=blue]<?= lang('project_launch_template_select_mc')?> yy - yyyyy[/color]
<br /> [*][color=#FF0000][b]<?= lang('project_launch_template_please_post_link')?>[/b][/color]
<br /> [*]<?= lang('project_launch_template_please_check_send_recording')?>[/list][/list]<p>
</div>