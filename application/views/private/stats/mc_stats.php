<?= $menu_header; ?>

<?php 

	$active_count = 0; 
	$total_count = 0;
	$table_body = '';

	foreach ($volunteers as $volunteer){
		$active_count += $volunteer->active_count;
		$total_count += $volunteer->max_projects;

		$color = 'black';
		if ($volunteer->active_count > $volunteer->max_projects) $color = 'red';
		if ($volunteer->active_count < ($volunteer->max_projects - $volunteer->max_projects*.15  )) $color = 'green';

		$table_body .= '<tr style="color: '.$color.' "><td>'.  $volunteer->username . '</td> <td>'.  $volunteer->active_count . '</td> <td>'.  $volunteer->max_projects . '</td> </tr>';
	}
?>


<div class="" >
	<div class="pull-left">

	<h4>Totals: Active: <?= $active_count; ?> / Max: <?= $total_count; ?> = <?= round(($active_count/$total_count) * 100, 1); ?>%</h4>

	<table class="table table-bordered">
		<thead>
			<tr><th>MC Forum Name</th> <th>Active Count</th> <th>Max Projects</th> </tr>
		</thead>

		<tbody>
			<?= $table_body; ?>
		</tbody>		
	</table>
	</div>

	<div class="pull-left span1"></div>


</div>

<div style="height:100px;"></div>