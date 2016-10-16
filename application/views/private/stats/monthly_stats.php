<?= $menu_header; ?>



<div class="" >
	<div class="pull-left span4">
		<table class="table table-bordered">
			<thead>
				<tr><th>Month - Year</th> <th>Project Count</th>  </tr>
			</thead>

			<tbody>
				<?php 
					foreach ($monthly_stats as $stat){
						echo '<tr><td>'.  $stat->date_catalog . '</td> <td>'.  $stat->project_count . '</td>  </tr>';
					}
				?>
			</tbody>		
		</table>
	</div>


	<div class="pull-left span4">
		<table class="table table-bordered">
			<thead>
				<tr><th>Year</th> <th>Project Count</th>  </tr>
			</thead>

			<tbody>
				<?php 
					foreach ($yearly_stats as $stat){
						echo '<tr><td>'.  $stat->date_catalog . '</td> <td>'.  $stat->project_count . '</td>  </tr>';
					}
				?>
			</tbody>		
		</table>
	</div>



	<div class="pull-left span1"></div>

</div>

<div style="height:100px;"></div>