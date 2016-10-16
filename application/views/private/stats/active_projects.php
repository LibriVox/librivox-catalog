<?= $menu_header; ?>


<table id="active_projects" class="table table-striped table-bordered table-hover table-condensed dataTable">
	<thead>
		<tr><th>Project Name</th> <th>Sections</th> <th>Assigned</th><th>%</th> <th>Completed</th><th>%</th><th>Status</th><th>PL</th><th>MC</th> </tr>
	</thead>

	<tbody>
		<?php foreach($projects as $project): ?>

		<tr>
			<td><a href="<?= $project->url_forum;  ?>"><?= $project->title ?></a></td>
			<td><?= $project->num_sections ?></td>
			<td><?= $project->assigned ?></td>
			<td><?= $project->assigned_pct ?></td>
			<td><?= $project->complete ?></td>
			<td><?= $project->complete_pct ?></td>
			<td><?= $project_statuses[$project->status] ?></td>
			<td><?= (isset($project_pls[$project->person_pl_id])) ? '<a href="'. base_url('reader/' . $project->person_pl_id ) .'">'.$project_pls[$project->person_pl_id] . '</a>' : ''; ?></td>
			<td><?= (isset($project_mcs[$project->person_mc_id])) ? '<a href="'. base_url('reader/' . $project->person_mc_id ) .'">'.$project_mcs[$project->person_mc_id] . '</a>' : ''; ?></td>
		</tr>


		<?php endforeach?>

	</tbody>	
</table>	
