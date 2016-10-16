<?= $menu_header; ?>

<div class="" >
	<div class="pull-left span4">
	<table class="table table-bordered">
		<tr><th>Total Active Projects</th> <td><?= $active_stats->total_projects?></td> </tr>
		<tr><th>Active Projects as MC</th> <td><?= $active_stats->total_projects_mc?></td> </tr>
		<tr><th>Active Projects as BC</th> <td><?= $active_stats->total_projects_bc?></td> </tr>
		<tr><th>Active Projects as PL</th> <td><?= $active_stats->total_projects_pl?></td> </tr>
		<tr><th>Active Projects as Reader</th> <td><?= $active_stats->total_projects_reader?></td> </tr>
		
	</table>
	</div>

	<div class="pull-left span1"></div>

	<div class="pull-left">
	<table class="table table-bordered">
		<tr><th>Total completed projects as MC</th> <td><?= $inactive_stats->total_projects_mc?></td> </tr>
		<tr><th>Total completed projects as BC</th> <td><?= $inactive_stats->total_projects_bc?></td> </tr>
		<tr><th>Total completed projects as PL</th> <td><?= $inactive_stats->total_projects_pl?></td> </tr>
		<tr><th>Total completed projects as Reader</th> <td><?= $inactive_stats->total_projects_reader?></td> </tr>				
		<tr><th>Total sections read</th> <td></td> </tr>
		<tr><th>Total time of completed sections</th> <td></td> </tr>
	</table>
	</div>
</div>

<div style="height:100px;"></div>