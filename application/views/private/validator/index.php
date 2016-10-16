
<?= $menu_header; ?>

<div class="" >

<div class="control-group">
  <div class="controls center" style="padding-top: 20px;">
  <h4 style="display:inline;margin-right: 300px;">Project <?= $project->id?>: <?= create_full_title($project)?></h4>    
  </div>
</div>  

<div id="response_message_validator" class="alert" style="display:none;"></div>

<?php if (empty($project->validator_dir)):?>
<div id="create_dir_div">	
	<div class="control-group">
	    <div class="controls center" style="padding-top: 20px;">
	    <h5>Please create a new directory for this project's files. The name should contain only letters, numbers, "_" and "-". </h5><h5>A suggested title has been pre-filled for you.</h5>	
	    <p>Please note: ".poem" and the "YYMM" will be automatically appended now - you should NOT add this yourself.</p>

	    <?= form_label('Directory name:',  'validator_dir'); ?>
	    <?= form_input(array('name'=> 'validator_dir', 'id' => 'validator_dir', 'class'=>'span8', 'value'=> $project->suggested_title )); ?>
	    <button id="create_dir_btn" class="btn btn-primary" style="vertical-align:top;">Create</button>
	    </div>
	</div> 
</div>
<?php endif; ?>


<?php $default_state = (empty($project->id) || empty($project->validator_dir) )? 'none' : 'inline'  ; ?>




<div id="validator" style="display:<?= $default_state ?>">

	<?php if (!$project->freeze): ?>
	
	<p id="uploader_toggle" class="toggle_form_btn btn" data-toggle_div_id="uploader" style="cursor:pointer;margin-bottom:20px;">Add new files</p>
	<p id="copy_project_files" class="btn"  style="cursor:pointer;margin-bottom:20px;">Add from Section Compiler</p>

	<p id="id3tags_edit_toggle" class="toggle_form_btn btn" data-toggle_div_id="id3tags_edit" style="cursor:pointer;margin-bottom:20px;">Edit ID3 tags</p>
	<a id="id3tags_edit_toggle" class="toggle_form_btn btn" href="<?= base_url()?>validator/adjust_file_volume/<?= $project->id?>" style="cursor:pointer;margin-bottom:20px;">Adjust volume</a>


	<?php endif; ?>

	<p id="run_tests" class="btn" style="cursor:pointer;margin-bottom:20px;">Runs Tests</p>

	<a id="section_compiler_link" href="<?= base_url()?>section_compiler/<?= $project->id?>" class="btn pull-right"  style="cursor:pointer;margin-bottom:20px;">Section Compiler</a>
	<a id="project_screen_link" href="<?= base_url()?>add_catalog_item/<?= $project->id?>" class="btn pull-right"  style="cursor:pointer;margin-bottom:20px;margin-right:4px;">Project Screen</a>
	
	<?php if (!$project->freeze): ?>

	<p id="upload_iarchive" class="btn pull-right"  style="cursor:pointer;margin-bottom:20px;margin-right:4px;">Upload to Archive.org</p>

	<?php endif; ?>


	<div id="id3tags_edit" style="display:none;margin-bottom: 12px;" >	
			<h5>Here are a few ways to edit all the files at once:</h5>	
			<label class="span2">Rename files to:</label>
			<input type="text" id="update_rename_files_1" value="<?= strtolower($project->suggested_title) ?>" />_sectionnumber_<input type="text" id="update_rename_files_2" value="<?= strtolower($project->author_last_name) ?>_128kb" />
			<button style="margin-left:10px;vertical-align:top;" id="update_rename_files" class="btn">Update</button>
			<br />

			<label class="span2">Set album to:</label>
			<input type="text" id="update_album"  value="<?= trim($project->full_title) ?>" />
			<button style="margin-left:10px;vertical-align:top;" id="update_set_album" class="btn">Update</button>
			<br />

			<label class="span2">Set artist to:</label>
			<input type="text" id="update_artist" value="<?= $project->author_full_name ?>" />
			<button style="margin-left:10px;vertical-align:top;" id="update_set_artist" class="btn">Update</button>
			<br />

			<label class="span2">Reset Track Numbers:</label>
			<button style="vertical-align:top;" id="reset_track_numbers" class="btn">Update</button>
			<br />			
	</div>


	<div id="uploader" style="display:none" >		
		<?= $this->load->view('private/validator/uploader', '', true); ?>
	</div>

	<div id="file_edit" style="display:none" class="control-group">	
		<div class="controls center">	
			<label class="span1">Name </label><input type="text" class="span4" id="edit_file_name" value="" data-original_name="" />
			<label style="display:inline-block;margin-left:30px;width:80px">Chapter </label> <input type="text" class="span3" id="edit_chapter_name" value=""  />
		</div>

		<div class="controls center">	
			<label class="span1">Album </label> <input type="text" class="span4" id="edit_album_name" value="" />
			<label style="display:inline-block;margin-left:30px;width:80px">Artist </label> <input type="text" class="span3" id="edit_artist_name" value=""  />
			<button style="margin-left:10px;" id="update_meta_data" class="btn">Update</button>
		</div>		
	</div>

	<div id="link_section" style="display:none" class="control-group">	
		<div class="controls center">
			<h5>Link file <span style="color:red;"id="link_section_file_name"></span> to a project section. This link will attempt to set the ID3 tags to the section info</h5>	
			<label class="span2">Section of project:</label><input type="text" class="span1" id="link_section_number" value="" data-original_name="" />
			<button style="margin-left:10px;vertical-align:top;" id="link_section_data" class="btn">Link</button>
		</div>
	
	</div>


	<div id="run_tests_div" style="display:none" >	</div>

	

	<div id="file_table">
		
		<?= $files_table ?>

	</div>

</div>

</div>

<div style="height:100px;"></div>	


<?= $uploader_modal; ?>