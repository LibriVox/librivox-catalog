<?= $header; ?>

<div class="main-content">

	<?= $sidebar;  ?>

	<div class="page reader-section- page">
		<input type="hidden" id="reader_id" value="<?= $reader->id?>"/>

		<div class="page-header-wrap">
			<div class="content-wrap clearfix">
				<h1><?= $reader->display_name?></h1>
				
				<div class="page-header-half">
					<?php if(!empty($reader->display_name)):?>
					<p><span>Catalog name:</span> <?= $reader->display_name?> </p>
					<?php endif; ?>

					<p><span>Forum name:</span> <?= $reader->username?></p>
					<p><a href="<?= base_url(). 'reader/' .$reader->id?>">Reader page</a></p>					
				</div>
				
				<div class="page-header-half">	
					<p><span>Total section:</span> <?= $sections ?></p>
					<p><span>Total matches:</span> <?= $matches ?></p>
					<p><span>URL:</span> <a href="<?= $reader->website?>"><?= $reader->website?></a></p>
				</div>	
			</div>	<!-- end .content-wrap clearfix -->
		</div><!-- end .page-header -->	


	<div class="">

		<div class="tabs nav nav-tabs">
			<a href="#reader_view" class="data_tab tab-btn selected" data-tab="reader_view" data-toggle="tab">Reader View</a>
			<a href="#pl_view" class="data_tab tab-btn" data-tab="pl_view" data-toggle="tab">PL view</a>
		</div>	

		<div class="content reader-page tab-content">
			<div class="tab-pane active" id="reader_view">
				<table class="table-list reader-view">
					<thead>
							<tr>
								<th class="first">Title</th>
								<th>Status</th>
								<th class="assigned">Assigned</th>
								<th>Ready for PL</th>
								<th>See PL notes</th>
								<th>Ready for spot check</th>
								<th>PL ok</th>						
							</tr>
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
		
			<div class="tab-pane" id="pl_view" style="display:none;">
				<table class="table-list pl-view">
					<thead>
							<tr>
								<th class="first">Title</th>
								<th>Status</th>
								<th class="third">Ready for PL</th>
								<th>See PL notes</th>
								<th>Ready for spot check</th>
								<th>PL ok</th>						
							</tr>
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

	</div><!-- end .reader-page-info -->



</div><!-- end .main-content -->

<?= $footer; ?>