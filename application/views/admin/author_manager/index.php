<?= $menu_header; ?>

<style type="text/css">
.tdfield{
    width: 100px;
    height: 25px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}
/* Tweak dataTables "Show X entries" control, so that it lines up with other buttons */
.dataTables_length {
	float: none;
	display: inline;
}

</style>

<div class="" >
	<div class="pull-left">

	<h4>Author Manager</h4>

	<div id="response_message_authormanager" class="alert" style="display:none;"></div>

	<div class="controls center" style="float:left;">
		<a style="display:inline;margin-right:10px;" class="btn" role="button" href="/admin/author_manager/unconfirmed">Load unconfirmed</a>
		<a style="display:inline;margin-right:10px;" class="btn" role="button" href="/admin/author_manager/all">Load all</a>
		<input style="display:inline;margin-right:10px;" class="btn" name="new_author_modal_btn" type="button" role="button" id="new_author_modal_btn" value="Add author" >
		<label style="display:inline;margin-right:5px;" for="author_search">Load author by name:</label>
		<input style="width:200px;display:inline;margin-right:10px;vertical-align:top;" type="text" id="search_author" class="autocomplete" data-search_field="full_name" data-search_area="author" data-search_func="autocomplete_author">
	</div>


	<table class="table table-bordered" id="authors_table">
		<thead>
			<tr> <th>Status</th> <th>Id</th> <th>First name</th> <th>Last name</th> <th>Pseudonyms</th> 
				<th>Url</th> <th>Blurb</th> <th>DOB</th> <th>DOD</th> 
				<th  class="data_filter" data-filter="status_filter">Confirm</th> 
				<th>Link to</th> <th>Wiki</th></tr>
		</thead>

		<tbody>
			<?php foreach ($authors as $author): ?>

				<tr id="author_row_<?= $author->id;?>" data-author_id="<?= $author->id;?>">
					<td><?= $author->confirmed;?></td>

					<td class="project_link" id="<?= $author->id ?>" style="cursor:pointer;" title="Double-click to see projects" data-author_name="<?= $author->first_name.' '.$author->last_name ?>"><?= $author->id;?></td> 

					<td id="first_name-<?= $author->id ?>" class="edit" ><?= $author->first_name;?></td>

					<?php $style = (empty( $author->last_name))? 'style="border:1px solid red;"' : ''; ?>
					<td id="last_name-<?= $author->id ?>"  class="edit" <?= $style;?> ><?= $author->last_name;?></td>

					<td ><div id="pseudonyms_<?= $author->id ?>" style="cursor:pointer;" title="Double-click to edit pseudonyms" class="pseudonyms_edit tdfield" data-author_id="<?= $author->id ?>" data-author_name="<?= $author->first_name.' '.$author->last_name ?>">Edit</div></td>				

					<?php $style = (empty( $author->author_url)) ?'style="border:1px solid red;"' : ''; ?>
					<td id="author_url-<?= $author->id ?>"  class="edit" <?= $style;?>><?= $author->author_url;?></td>

					<td ><div id="blurb_<?= $author->id ?>" style="cursor:pointer;" title="Double-click to edit blurb" class="blurb_edit tdfield" data-author_id="<?= $author->id ?>" data-author_name="<?= $author->first_name.' '.$author->last_name ?>"><?= $author->blurb;?></div></td>

					<?php $style = (!empty($author->dob) && (!preg_match('/^\d{4}$/', $author->dob)))? 'style="border:1px solid red;"' : ''; ?>  
					<td id="dob-<?= $author->id ?>"  class="edit" <?= $style;?>><?= $author->dob;?></td> 

					<?php $style = (!empty($author->dod) && (!preg_match('/^\d{4}$/', $author->dod)))? 'style="border:1px solid red;"' : ''; ?>  
					<td id="dod-<?= $author->id ?>"  class="edit" <?= $style;?>><?= $author->dod;?></td>

					<?php 
						if ($author->confirmed)
						{
							$label = 'Reopen';
							$class = 'btn-success';
						}
						else
						{
							$label = 'Confirm';
							$class = 'filter_on_me';
						}

					?>

					<td><span class="confirm_author btn <?= $class ?>" id="confirmed-<?= $author->id ?>" data-status="<?= $author->confirmed ?>" ><?= $label ?></span></td>

					<td id="linked_to-<?= $author->id ?>" class="edit" ><?= $author->linked_to;?></td>					

					<?php 
						//we'll try to make th eurl if it doesn't exist

						if(empty($author->author_url))
						{
							$wiki_base_url 	= 'http://en.wikipedia.org/wiki/';
							$search = array(' ', '.', ',');
							$replace = array('_', '', '');
							$full_name 		= str_replace($search, $replace, $author->first_name.' '.$author->last_name);
							$url = $wiki_base_url.$full_name; 
						}
						else
						{
							$url =  $author->author_url;
						}	

					?>

					<td><a href="<?= $url?>" target = "_blank"><i class="icon-edit"></i></a></td>		
				</tr>

			<?php endforeach; ?>
		</tbody>		
	</table>
	</div>

	<div class="pull-left span1"></div>


</div>

<div style="height:100px;"></div>


<?= $author_blurb_modal; ?>

<?= $author_projects_modal; ?>

<?= $author_pseudonyms_modal; ?>

<?= $author_new_modal; ?> 
