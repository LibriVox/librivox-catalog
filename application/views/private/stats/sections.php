<?= $menu_header; ?>


<div class="" >
	<div class="pull-left">

	    <ul class="nav nav-tabs">
		    <li class="active"><a href="#reader_view" data-toggle="tab">Reader View</a></li>
		    <li><a href="#pl_view" data-toggle="tab">PL View</a></li>
	    </ul>

		<div class="tab-content">
			<div class="tab-pane active" id="reader_view">
				<table class="table table-bordered">
					<thead>
						<tr><th>Title</th> <th>Status</th> <th>Assigned</th> <th>Ready for PL</th> <th>See PL Notes</th> <th>Ready for Spot Check</th> <th>PL OK</th> </tr>
					</thead>

					<tbody>
						<?php if(!empty($reader_stats)): foreach($reader_stats as $project): ?>
							<tr>
								<td class="first"><a href="<?= $project['url_forum']?>"><?= $project['title']?></a></td>
								<td><?= $project_statuses[$project['status']]?></td>
								<td><?= (isset($project['assigned'])) ? $project['assigned'] : ''; ?></td>
								<td><?= (isset($project['ready_for_pl'])) ? $project['ready_for_pl'] : ''; ?></td>
								<td><?= (isset($project['see_pl_notes'])) ? $project['see_pl_notes'] : ''; ?></td>
								<td><?= (isset($project['ready_for_spot_pl'])) ? $project['ready_for_spot_pl'] : ''; ?></td>
								<td><?= (isset($project['pl_ok'])) ? $project['pl_ok'] : ''; ?></td>
							</tr>
						<?php endforeach;endif;?>
					</tbody>		
				</table>
			</div>



			<div class="tab-pane" id="pl_view">
				<table class="table table-bordered">
					<thead>
						<tr><th>Title</th> <th>Status</th> <th>Ready for PL</th> <th>See PL Notes</th> <th>Ready for Spot Check</th> <th>PL OK</th>  </tr>
					</thead>

					<tbody>
						<?php if(!empty($pl_stats)): foreach($pl_stats as $project): ?>
							<tr>
								<td class="first"><a href="<?= $project['url_forum']?>"><?= $project['title']?></a></td>
								<td><?= $project_statuses[$project['status']]?></td>								
								<td><?= (isset($project['ready_for_pl'])) ? $project['ready_for_pl'] : ''; ?></td>
								<td><?= (isset($project['see_pl_notes'])) ? $project['see_pl_notes'] : ''; ?></td>
								<td><?= (isset($project['ready_for_spot_pl'])) ? $project['ready_for_spot_pl'] : ''; ?></td>
								<td><?= (isset($project['pl_ok'])) ? $project['pl_ok'] : ''; ?></td>
							</tr>
						<?php endforeach;endif;?>
					</tbody>	
				</table>

			</div>
		</div>



	</div>

	<div class="pull-left span1"></div>


</div>

<div style="height:100px;"></div>