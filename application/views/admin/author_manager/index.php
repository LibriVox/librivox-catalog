<?= $menu_header; ?>

<style type="text/css">
.tdfield{
    width: 100px;
    height: 25px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}

</style>

<div class="" >
	<div class="pull-left">

	<h4>Author Manager</h4>

	<div data-filter="status_filter" id="status_filter" style="margin-bottom: 20px;">	
		<span style="vertical-align:middle;" >Show only unconfirmed authors: </span>
		<input style="margin-left:10px;vertical-align:middle;" class="status_group" name="status_group[]" type="radio" id="active_status" value="1" checked> 
		<span style="margin-left:10px;vertical-align:middle;" >Show all authors: </span>
		<input style="margin-left:10px;vertical-align:middle;" class="status_group"  name="status_group[]" type="radio" id="all_status" value="all" > 	

		<input style="margin-left:20px;vertical-align:middle;" class="btn"  name="new_author_modal_btn" type="button" id="new_author_modal_btn" value="New Author" >
	</div>


	<table class="table table-bordered" id="authors_table">
		<thead>
			<tr> <th>Status</th> <th>Id</th> <th>First name</th> <th>Last name</th> <th>Pseudonyms</th> 
				<th>Url</th> <th>Blurb</th> <th>DOB</th> <th>DOD</th> 
				<th  class="data_filter" data-filter="status_filter">Confirm</th> 
				<th>Link to</th> <th>Wiki</th> <th>Image URL</th></tr>
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

					<td  id="image_url-<?= $author->id ?>"  class="edit" ><?= $author->image_url;?></td>					
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
