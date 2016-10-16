<?= $menu_header; ?>


<table style="width: 500px;">
	<thead>
		<tr><th>Category</th> <th>Count</th> </tr>
	</thead>

	<tbody>
		<tr></tr>
		<tr><td>Total number of projects</td><td><?= $project_count;?></td></tr>
		<tr><td>Number of completed projects</td><td><?= $project_count_completed;?></td></tr>
		<tr><td>Number of completed non-English projects</td><td><?= $project_count_completed_nonenglish;?></td></tr>
		<tr><td>Total number of languages</td><td><?= $language_count;?></td></tr>
		<tr><td>Number of languages with a completed work</td><td><?= $language_count_with_completed;?></td></tr>
		<tr><td>Number of completed solo projects</td><td><?= $project_count_completed_solo;?></td></tr>
		<tr><td>Number of readers</td><td><?= $reader_count;?></td></tr>
		<tr><td>...who have completed something</td><td><?= $reader_count_with_completed;?></td></tr>
	</tbody>	

</tbody></table>