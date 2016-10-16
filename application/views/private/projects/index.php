<?= $menu_header; ?>


<div data-filter="status_filter" id="status_filter">	
	<span style="vertical-align:middle;" >Show only active projects: </span>
	<input style="margin-left:10px;vertical-align:middle;" class="status_group" name="status_group[]" type="radio" id="active_status" value="1" > 
	<span style="margin-left:10px;vertical-align:middle;" >Show all projects: </span>
	<input style="margin-left:10px;vertical-align:middle;" class="status_group"  name="status_group[]" type="radio" id="all_status" value="all" checked> 	
</div>

<br/>

<div class="" >



	<table id="projects_table" class="table table-striped table-bordered table-hover table-condensed" >
		<thead>
			<tr><th class="data_filter" data-filter="status_filter">status_group</th>
				<th>Id</th><th>SC</th>
				<?php if ($view_validator):?>
					<th>V</th>
				<?php endif;?>
				<th>Title</th><th>Author</th><th>Status</th><th>Forum Url</th><th>Catalog Url</th>
				<?php if ($user_projects):?>
					<th>as BC</th><th>as MC</th><th>as PL</th><th>as Reader</th></tr>
				<?php endif;?>
		</thead>
		<tbody>
			<?php foreach ($projects as $project): ?>
				<tr id="<?= $project->id ?>">
					<td><?= $project->status_group;    ?></td>
					<td id="id-<?= $project->id ?>" class="id"><a href="<?= base_url().'add_catalog_item/'.$project->id ?>"><?= $project->id ?></a></td>

					<?php if ($project->view_compiler):?>
					<td><a href="<?= base_url().'section_compiler/'.$project->id ?>"><i class="icon-search meta_data"></i></a></td>
					<?php else:?>
					<td></td>
					<?php endif;?>
					
					<?php if ($view_validator):?>
						<td><a href="<?= base_url().'validator/'.$project->id ?>"><i class="icon-search meta_data"></i></a></td>
					<?php endif;?>

					<td id="title-<?= $project->id ?>" ><?= $project->title ?></td>
					<td id="author-<?= $project->id ?>" ><?= $project->author ?></td>
					<td id="status-<?= $project->id ?>" ><?= $project_statuses[$project->status] ?></td>
					<td id="url_forum-<?= $project->id ?>" ><a href="<?= $project->url_forum ?>">link</a></td>
					<td id="url_librivox-<?= $project->id ?>" ><a href="<?= $project->url_librivox ?>">link</a></td>
					<?php if ($user_projects):?>
					<td id="bc-<?= $project->id ?>" ><?= $project->bc ?></td>
					<td id="mc-<?= $project->id ?>" ><?= $project->mc ?></td>
					<td id="pl-<?= $project->id ?>" ><?= $project->pl ?></td>
					<td id="reader-<?= $project->id ?>" ><?= $project->reader ?></td>
					<?php endif;?>
				</tr>
			<?php endforeach; ?>

		</tbody>
	</table>

</div>

<div style="height:100px;"></div>