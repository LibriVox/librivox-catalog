<script type="text/javascript">
	var PEOPLE_LINK = "<?= PEOPLE_LINK ?>";
</script>

<?= $menu_header; ?>

<div class="" >

<div class="control-group">
  <div class="controls center" style="padding-top: 20px;">
  <h4 style="display:inline;margin-right: 300px;">Project <?= $project->id?>: <?= create_full_title($project)?></h4>    
  </div>
</div>  

<div id="response_message" class="alert" style="display:none"></div>

<div class="control-group">
  <div class="controls center" style="padding-top: 20px;">
  <h4 id="add_section_toggle" class="btn add_btn" data-toggle_div="add_section_div" style="display:inline;margin-right: 10px;">Add new section</h4>  
  <h4 id="add_reader_toggle" class="btn  profile_modal_link"style="display:inline;margin-right: 10px;">Add new reader</h4>  
  <h4 id="assign_reader_toggle" class="btn add_btn" data-toggle_div="assign_reader_div" style="display:inline;margin-right: 20px;">Assign reader sections</h4>  

  <?php if ($admin_mc):?>
  	<div style="display:inline;margin-right: 10px;">Project Status: <?= form_dropdown('status', $statuses, $project->status, 'id="status" style="margin-bottom: 0px !important;"'); ?></div>
  	<h4 style="display:inline;" class="pull-right"><a class="btn" href="<?= base_url()?>validator/<?= $project->id?>">Validate Project</a></h4>
  <?php endif; ?>

  <h4 style="display:inline;margin-right: 10px;" class="pull-right"><a class="btn" href="<?= base_url()?>add_catalog_item/<?= $project->id?>">Project Screen</a></h4>
  </div>
</div>  



<div id="add_section_div" class="toggle_div" style="display:none">
	<table>
		<thead>
			<tr><th>Title</th><th>Reader</th><th>Notes</th><th></th></tr>
		</thead>
		<tbody>
			<tr>

				<td><input type="text" id="add_section_title" ></td>
				<td><input type="text" id="add_section_reader" class="autocomplete" data-assign_reader_id="0" data-search_field="username" data-search_area="section_reader" data-search_func="search_readers" data-array_index="1">
</td>
				<td><input type="text" id="add_section_notes" ></td>
				<td style="vertical-align: top !important;" >
					<input type="button" id="add_section_btn" class="btn" value="Add" />
					<input type="button"  class="btn cancel_btn" value="Cancel" />
				</td>
			</tr>
		</tbody>		
	</table>
</div>

<div id="add_section_info_div" class="toggle_div" style="display:none;margin-top: 30px;">

	<h5>Enter author, source and language for sections in collections or compilations only. Click Add to save.</h5>

	<table>
		<thead>
			<tr><th>Section</th><th>Author <span style="font-size:10px;">(last name search)</span></th><th>Source</th><th>Language</th><th>Duration</th><th></th></tr>
		</thead>
		<tbody>
			<tr>
				<td><input style="width:40px;" type="text" id="add_to_section_number" ><input type="hidden" id="add_to_section_id" value="0"></td>
				<td><input style="width:200px;" type="text" id="add_author" class="autocomplete" data-add_author_id="0" data-search_field="last_name" data-search_area="author" data-search_func="add_project" data-array_index="1"></td>
				<td><input style="width:160px;" type="text" id="add_source" ></td>
				<td><?= $recorded_languages; ?></td>
				<td><input style="width:80px;" type="text" id="add_playtime" placeholder="00:00:00"></td>
				<td style="vertical-align: top !important;" >
					<input type="button" id="add_section_info_btn" class="btn" value="Add" />
					<input type="button"  class="btn cancel_btn" value="Cancel" />
				</td>
			</tr>

			<tr>
				<td>MP3 64 url: </td><td colspan="5"><input style="width:820px;" type="text" id="add_mp3_64_url" ></td>
			</tr>
			<tr>
				<td>MP3 128 url: </td><td colspan="5"><input style="width:820px;" type="text" id="add_mp3_128_url" ></td>
			</tr>
		</tbody>		
	</table>
</div>


<div id="assign_reader_div" class="toggle_div" style="display:none">
	<h5>Chose a reader and enter which sections thay are assigned to. You may use numbers separated by commas or ranges with hyphens. (i.e., 1,2,4-7,8)</h5>
	<table>
		<thead>
			<tr><th>Reader</th><th>Sections</th><th colspan="2"></th></tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="text" id="assign_reader" class="autocomplete" data-assign_reader_id="0" data-search_field="username" data-search_area="reader" data-search_func="search_readers" data-array_index="1"></td>
				<td><input type="text" id="assign_section" ></td>
				<td style="vertical-align: top !important;" >
					<input type="button" id="assign_reader_btn" class="btn" value="Add" />
					<input type="button" id="remove_reader_btn" class="btn" value="Remove" />
					<input type="button"  class="btn cancel_btn" value="Cancel" />
				</td>
			</tr>
		</tbody>		
	</table>
</div>




<input type="hidden" id="project_id" value="<?= $project_id ?>">
<input type="hidden" id="has_preface" value="<?= $project->has_preface ?>">

<h5>To edit the sections below, double-click the table cell. Hit "Enter" to save or "Escape" to cancel your change.</h5>

<h5>To reorder sections, simply drag & drop the section to its new place. All section numbers will be updated for you.</h5>

<table id="m_sections_table" class="table table-striped table-bordered table-hover table-condensed" >
	<thead>
		<tr><th class="section_number_header">Section</th><th>Meta</th><th>Title</th><th>Reader</th><th>Notes</th><th>Listen URL</th><th>Link</th><th>Status</th>
			<?php if ($admin_mc):?>
			<th></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($sections)): ?>
		<?php foreach ($sections as $section): ?>

			<?php 
				//yes, this should go into some sort of previewer class, but...
				$reader_link = '';
				if (!empty($section->readers))
				{
					foreach ($section->readers as $key => $reader) {
						$reader_link .= '<a href="'.PEOPLE_LINK.$reader->reader_id.'">'.$reader->reader_name.'</a> ';  	
					}
				}


				$listento_link = '';
				if (!empty($section->listen_url))
				{				
					$listento_link .= '<a href="'.$section->listen_url.'">Link</a> ';  	
				}	

				if($project->status == PROJECT_STATUS_COMPLETE)
				{
					$section->notes = '';
	                $section->listen_url = '';
	                $listento_link = '';
				}	



			?>


			<tr id="<?= $section->id ?>" data-section_number="<?= $section->section_number ?>">
				<td id="section_number-<?= $section->id ?>" class="section_number " style="width: 20px;"><?= str_pad($section->section_number, 2, '0', STR_PAD_LEFT) ?> </td>
				<td><i class="icon-search meta_data" data-section_id="<?= $section->id ?>"  data-placement="left" rel="tooltip" data-original-title="Section Metadata"></i></td>
				<td id="title-<?= $section->id ?>" class="edit"><?= $section->title ?></td>
				<td id="reader-<?= $section->id ?>" class="reader" style="cursor:default;"><?= $reader_link ?></td>
				<td id="notes-<?= $section->id ?>" class="edit_area"><?= $section->notes ?></td>
				<td id="listen_url-<?= $section->id ?>" data-id="<?= $section->id ?>" class="edit"><?= $section->listen_url ?></td>
				<td id="listen_link_url-<?= $section->id ?>"  style="cursor:default;"><?= $listento_link ?></td>

				<td id="status-<?= $section->id ?>" class="edit_status"><?= $section->status ?></td>

				<?php if ($admin_mc):?>
				<td><i data-section_id="<?= $section->id ?>" data-placement="left" rel="tooltip" class="icon-remove delete_section" data-original-title="Remove Section"></i></td>
				<?php endif ?>

			</tr>
		<?php endforeach; ?>
		<?php endif?>

	</tbody>
</table>

</div>

<div style="height:100px;"></div>