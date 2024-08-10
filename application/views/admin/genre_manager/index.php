<?= $menu_header; ?>

<div class="" >
	<div class="pull-left">

	<div><h4>Genre Manager</h4><button class="btn btn-small btn-primary toggle_form_btn" data-toggle_div_id="add_new_genre">Add new</button></div>


	<div id="add_new_genre" style="display:none;">
		<table>
			<thead>
				<tr>
					<th>Genre</th><th>Parent</th><th>Sort Order</th><th></th>
				</tr>
			</thead>			

			<tbody>
				<tr>
					<td><input id="genre_0"  style="width:350px;"/></td>
					<td style="width:320px;"> <?= form_dropdown('parent_id_0' , $genre_dropdown, 0, 'id="parent_id_0"');?> </td>
					<td><input id="sort_order_0" style="width:30px; text-align:right" value="20"></input></td>
					<td><button class="btn btn-small btn-primary save_genre" data-genre_id="0">Save</button></td>
				</tr>
			</tbody>
		</table>

	</div>



	<table class="table table-striped table-condensed table-hover" style="margin-top: 30px;">

		<thead>
			<tr>
				<th>Genre</th>
				<th>Parent</th>
				<th rel="tooltip" class="tooltip-host" data-original-title="Override the order of items in each menu.  Lower numbers are sorted first.  Matching numbers are sorted alphabetically.">
					Sort Order
				</th>
				<th></th>
			</tr>
		</thead>

		<tbody>
		<?php foreach ($genres as $key=>$genre): ?>
			<tr>
				<td><input id="genre_<?= $genre['id']?>" value="<?= $genre['name']?>"  style="width:350px;margin-left:<?= $genre['deep'] * 40?>px; "/></td>
				<td style="width:320px;"> <?= form_dropdown('parent_id_'.$genre['id'] , $genre_dropdown, $genre['parent_id'], 'id="parent_id_'.$genre['id'].'"');?> </td>
				<td><input style="width:30px; text-align:right" id="sort_order_<?= $genre['id']?>" value="<?= $genre['sort_order'];?>"> </input></td>
				<td><button class="btn btn-small btn-primary save_genre" data-genre_id="<?= $genre['id']?>">Save</button></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	</div>
</div>