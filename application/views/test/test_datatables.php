
<table class="table table-bordered" id="authors_table">
	<thead>
		<tr><th>First Name</th><th>Last Name</th> </tr>
	</thead>
	<tbody>
		<?php foreach ($authors as $author): ?>
		<tr>
			<td class="edit"><?= $author->first_name ?></td>
			<td class="edit"><?= $author->last_name ?></td> 
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
