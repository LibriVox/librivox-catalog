<!doctype html>
<head>
	<title>Librivox Project iFrame</title>
	<meta charset="utf-8">


	<style type="text/css">

		body {
		    background: none repeat scroll 0 0 #FFFFFF;
		    font-family: serif;
		    font-size: 15px;
		}
		a {
		    color: #6633FF;
		    text-decoration: underline;
		}
		a:visited {
		    color: #6633FF;
		    text-decoration: none;
		}
		a:hover {
		    text-decoration: underline;
		}
		table {
		    border: 1px solid #CCCCCC;
		    margin: 20px 0 0;
		}

		table th {
			font-weight: bolder;
			border: 0 none;
		    background: none repeat scroll 0 center #E0EBEF;
		    border: 0 none;
		    font-weight: bolder;
		    padding: 3px;
		    vertical-align: top;
		}

		table td {
		    background: none repeat scroll 0 0 #E0EBEF;
		    border: 0 none;
		    padding: 3px;
		    vertical-align: top;
		}

		tr.complete td {
		    background-color: #E9F3C7;
		}
		span.complete {
		    background-color: #E9F3C7;
		}

		tr.assigned td {
		    background-color: #EFEFEF;
		}
		.assigned {
		    background-color: #EFEFEF;
		}

		tr.open td {
		    background-color: #E0EBEF;
		}
		.open {
		    background-color: #E0EBEF;
		}

		.navy_blue{
			color: #0000BF;
		}
		.deep_red{
			color: #BF0000;
		}

		tr.needspl td {
		    background-color: #EFEF80;
		}
		tr.needsfixing td {
		    background-color: #EF8080;
		}
		label {
		    font-weight: bolder;
		}
		p {
		    text-indent: 5px;
		}
		p.note {
		    width: 600px;
		}




	</style>




</head>

<body>


	<?php

	/*

	Open - Blue
	Assigned - Grey
	Ready for PL - Grey
	See PL Notes - Grey
	Ready for Spot PL - Grey
	PL OK - Babypuke green

	// color
		/*
		Ready for PL - #0000BF
		See PL Notes - #BF0000
		Ready for Spot PL - #0000BF

		*/	


	?>



	<div id="info_instructions">
		<p>Completed chapters are marked in <span class="complete">this color</span>.</p>
		<p>Assigned chapters are marked in <span class="assigned">this color</span>.</p>

		<p><?= $section_assigned ?> of <?= $section_count ?> (<?= round(safe_divide($section_assigned, $section_count , 0)*100,0) ?>%) sections assigned</p>
		<p><?= $section_complete ?> of <?= $section_count ?> (<?= round(safe_divide($section_complete, $section_count , 0)*100,0) ?>%) sections completed</p>

		<?php if (!empty($pl_username)): ?>
		<p>This project has a dedicated proof-listener who will listen to all sections: <?= $pl_username?></p>
		<?php endif;?>

	</div>

	<div id="info_table">

		<table>

			<thead>
				<tr>
					<th>Section</th><th>Title</th><th>Reader</th><th>Notes</th><th>Listen Url</th><th>Status</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($section_info as $key => $section):  ?> 
					<?php 
						$tr_class = '';
						$status = strtolower(trim($section->status));
						if ($status == 'open') 
						{
							$tr_class = ' open ';
						}
						elseif ($status == 'pl ok') 
						{
							$tr_class = ' complete ';
						}
						else
						{
							$tr_class = ' assigned ';
						}

						// font color
						if ($status == 'ready for pl' || $status == 'ready for spot pl') 
						{
							$tr_class .= ' navy_blue ';
						}						
						if ($status == 'see pl notes' ) 
						{
							$tr_class .= ' deep_red ';
						}	

					?>


					<tr class="<?= $tr_class;?>">
						<td><?= $section->section_number ?></td>
						<td><?= $section->title ?></td>


						

						<?php 
							$reader_link = '&nbsp;';
							if (!empty($section->readers))
							{
								foreach ($section->readers as $key => $reader) {
									$reader_link .= '<a href="'.PEOPLE_LINK.$reader->reader_id.'">'.$reader->reader_name.'</a> ';  	
								}
							}

							if($project->status == PROJECT_STATUS_COMPLETE)
							{
								$section->notes = '';
				                $section->listen_url = '';
							}	

						?>

						<td><?= $reader_link ?></td>


						<td><?= $section->notes ?></td>
						<?php $listen = (empty($section->listen_url))? '': 'Listen';  ?>
						<td><a href="<?= $section->listen_url ?>"><?= $listen; ?></a></td>
						<td><?= $section->status ?></td>


					</tr>

				<?php endforeach; ?>

			</tbody>

		</table>


	</div>


</body>
</html>