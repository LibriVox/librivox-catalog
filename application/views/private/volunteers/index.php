<?= $menu_header; ?>

<div class="" >

	<table id="volunteers_table" class="table table-striped table-bordered table-hover table-condensed" >
		<thead>
			<tr><th>Id</th>
				<?php if ($show_edit):?>
				<th>Edit</th>
				<?php endif; ?>
			<th>Name</th>
			<th>Display Name</th>
				<?php if ($show_edit):?>
				<th>Email</th>
				<?php endif; ?>
			<th>Website</th></tr>
		</thead>
		<tbody>
			<?php foreach ($volunteers as $volunteer): ?>
				<tr id="<?= $volunteer->id ?>">
					<td id="id-<?= $volunteer->id ?>" class="id"><?= $volunteer->id ?></td>
					<?php if ($show_edit):?>
						<td><i class="icon-search user_meta_data" data-volunteer_id="<?= $volunteer->id ?>" style="cursor:pointer;"></i></td>
					<?php endif; ?>
					<td id="name-<?= $volunteer->id ?>" class="edit"><a href="<?= PEOPLE_LINK.$volunteer->id  ?>"><?= $volunteer->username ?></a></td>
					<td id="display_name-<?= $volunteer->id ?>" class="edit"><?= $volunteer->display_name ?></td>
					<?php if ($show_edit):?>
						<td id="email-<?= $volunteer->id ?>" class="edit"><a href="mailto:<?= $volunteer->email ?>"><?= $volunteer->email ?></a></td>
					<?php endif; ?>	
					<td id="website-<?= $volunteer->id ?>" class="edit"><?= $volunteer->website ?></td>
				</tr>
			<?php endforeach; ?>

		</tbody>
	</table>

</div>

<div style="height:100px;"></div>