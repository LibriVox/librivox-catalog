<?= $menu_header; ?>

<style>
	table thead th, table tbody td {
		white-space: nowrap;
	}

	table thead th.wrapped-cell, table tbody td.wrapped-cell {
		white-space: normal;
		min-width: 300px !important;
	}

	.table-actions {
		padding-bottom: 0.5rem;
	}
	.table-actions label {
		display: inline-block;
	}
	.table-actions select {
		margin: 0;
	}
	.table-actions .pagination, .table-actions .table-info {
		margin-top: 10px;
		margin-bottom: 10px;
	}

	#authors_table_wrapper {
		overflow-x: scroll;
		-ms-overflow-x: scroll;
		scrollbar-gutter: both-edges;
	}

	#authors_table_wrapper > table {
		white-space: nowrap;
	}

	.text-right {
		text-align: right;
	}

	.btn .btn-icon {
		margin: auto 0 !important;
	}
</style>

<div class="container grid">
	<div class="row">
		<h4 class="span6">Author Manager</h4>
		<div class="span6 text-right">
			<div class="input-append">
				<input class="span2" id="authors_table_search" type="text" placeholder="Search..."
					   value="<?= $searchTerm ?>">
				<button id="authors_table_search_btn" type="button" class="btn btn-primary">Search</button>
				<button id="authors_table_clear_btn" class="btn btn-success" type="button">Clear</button>
			</div>
		</div>
	</div>

	<div id="status_filter" data-filter="status_filter" class="row table-actions">
		<div class="span6">
			<label for="author_confirmed_dropdown">
				Authors
			<select id="author_confirmed_dropdown" class="span2">
					<option value=""></option>
					<option value="1" <?php if ($confirmed === '1' || $confirmed === 1): ?>selected<?php endif; ?>>
						Confirmed
					</option>
					<option value="0" <?php if ($confirmed === '0' || $confirmed === 0): ?>selected<?php endif; ?>>
						Unconfirmed
					</option>
				</select>
			</label>
		</div>

		<div class="span6 text-right">
			<input style="margin-left:20px;vertical-align:middle;" class="btn" name="new_author_modal_btn"
				   type="button"
				   id="new_author_modal_btn" value="New Author">

			<button id="scroll-left-button" class="btn btn-primary">
				<i class="btn-icon icon-chevron-left icon-white"></i>
				Scroll Left
			</button>
			<button id="scroll-right-button" class="btn btn-primary">
				Scroll Right
				<i class="btn-icon icon-chevron-right icon-white"></i>
			</button>

			<select id="authors_table_length_dropdown" class="span1">
				<option value="10" <?php if ($length == 10): ?>selected<?php endif; ?>>10</option>
				<option value="25" <?php if ($length == 25): ?>selected<?php endif; ?>>25</option>
				<option value="100" <?php if ($length == 100): ?>selected<?php endif; ?>>100</option>
			</select>
			Items/page
		</div>
	</div>

	<div class="row">
		<div class="span12">
			<table id="authors_table" class="table table-bordered" style="margin-bottom: 0">
				<thead>
				<tr>
					<th>Status</th>
					<th>Id</th>
					<th>First name</th>
					<th>Last name</th>
					<th>Pseudonyms</th>
					<th>Url</th>
					<th class="wrapped-cell">Blurb</th>
					<th>DOB</th>
					<th>DOD</th>
					<th class="data_filter" data-filter="status_filter">Confirm</th>
					<th>Link to</th>
					<th>Wiki</th>
					<th class="wrapped-cell">Image URL</th>
				</tr>
				</thead>

				<tbody>
				<?php foreach ($authors as $author): ?>

					<tr id="author_row_<?= $author->id; ?>" data-author_id="<?= $author->id; ?>">
						<td><?= $author->confirmed; ?></td>

						<td class="project_link" id="<?= $author->id ?>" style="cursor:pointer;"
							title="Double-click to see projects"
							data-author_name="<?= $author->first_name . ' ' . $author->last_name ?>"><?= $author->id; ?></td>

						<td id="first_name-<?= $author->id ?>" class="edit"><?= $author->first_name; ?></td>

						<?php $style = (empty($author->last_name)) ? 'style="border:1px solid red;"' : ''; ?>
						<td id="last_name-<?= $author->id ?>"
							class="edit" <?= $style; ?> ><?= $author->last_name; ?></td>

						<td>
							<div id="pseudonyms_<?= $author->id ?>" style="cursor:pointer;"
								 title="Double-click to edit pseudonyms" class="pseudonyms_edit tdfield"
								 data-author_id="<?= $author->id ?>"
								 data-author_name="<?= $author->first_name . ' ' . $author->last_name ?>">Edit
							</div>
						</td>

						<?php $style = (empty($author->author_url)) ? 'style="border:1px solid red;"' : ''; ?>
						<td id="author_url-<?= $author->id ?>"
							class="edit" <?= $style; ?>><?= $author->author_url; ?></td>

						<td class="wrapped-cell">
							<div id="blurb_<?= $author->id ?>" style="cursor:pointer;"
								 title="Double-click to edit blurb"
								 class="blurb_edit tdfield" data-author_id="<?= $author->id ?>"
								 data-author_name="<?= $author->first_name . ' ' . $author->last_name ?>"><?= $author->blurb; ?></div>
						</td>

						<?php $style = (!empty($author->dob) && (!preg_match('/^\d{4}$/', $author->dob))) ? 'style="border:1px solid red;"' : ''; ?>
						<td id="dob-<?= $author->id ?>" class="edit" <?= $style; ?>><?= $author->dob; ?></td>

						<?php $style = (!empty($author->dod) && (!preg_match('/^\d{4}$/', $author->dod))) ? 'style="border:1px solid red;"' : ''; ?>
						<td id="dod-<?= $author->id ?>" class="edit" <?= $style; ?>><?= $author->dod; ?></td>

						<?php
						if ($author->confirmed)
						{
							$label = 'Reopen';
							$class = 'btn-success';
						}
						else
						{
							$label = 'Confirm';
							$class = 'filter_on_me';
						}

						?>

						<td><span class="confirm_author btn <?= $class ?>" id="confirmed-<?= $author->id ?>"
								  data-status="<?= $author->confirmed ?>"><?= $label ?></span></td>

						<td id="linked_to-<?= $author->id ?>" class="edit"><?= $author->linked_to; ?></td>

						<?php
						//we'll try to make th eurl if it doesn't exist

						if (empty($author->author_url))
						{
							$wiki_base_url = 'http://en.wikipedia.org/wiki/';
							$search = array(' ', '.', ',');
							$replace = array('_', '', '');
							$full_name = str_replace($search, $replace, $author->first_name . ' ' . $author->last_name);
							$url = $wiki_base_url . $full_name;
						}
						else
						{
							$url = $author->author_url;
						}

						?>


						<td><a href="<?= $url ?>" target="_blank"><i class="icon-edit"></i></a></td>

						<td id="image_url-<?= $author->id ?>" class="wrapped-cell edit"><?= $author->image_url; ?></td>
					</tr>

				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row table-actions">
		<div class="span4 table-info">
			Showing <?= (max($page - 1, 0) * $length) + 1 ?> to <?= min($page * $length, $filtered_count) ?>
			of <?= $filtered_count ?>
			<?php if ($filtered_count < $total_count): ?>
				(filtered out of <?= $total_count ?>)
			<?php endif; ?>
		</div>

		<div class="offset3 span5 text-right">
			<div id="authors_table_pagination" class="pagination">
				<ul>
					<?php if ($page > 1): ?>
						<li><a href="#" class="page-first">First</a></li>
					<?php else: ?>
						<li class="disabled"><a>First</a></li>
					<?php endif ?>
					<?php if ($page > 1): ?>
						<li><a href="#" class="page-prev">Prev</a></li>
					<?php else: ?>
						<li class="disabled"><a>Prev</a></li>
					<?php endif ?>

					<?php foreach ($pages as $p): ?>
						<?php if ($page == $p): ?>
							<li class="disabled"><a><?= $p ?></a></li>
						<?php else: ?>
							<li><a class="page-goto" data-page="<?= $p ?>" href="#"><?= $p ?></a></li>
						<?php endif ?>
					<?php endforeach; ?>

					<?php if ($page < $page_count): ?>
						<li><a href="#" class="page-next">Next</a></li>
					<?php else: ?>
						<li class="disabled"><a>First</a></li>
					<?php endif ?>
					<?php if ($page < $page_count): ?>
						<li><a data-page="<?= $page_count ?>" href="#" class="page-last">Last</a></li>
					<?php else: ?>
						<li class="disabled"><a>Last</a></li>
					<?php endif ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<div style="height:100px;"></div>


<?= $author_blurb_modal; ?>

<?= $author_projects_modal; ?>

<?= $author_pseudonyms_modal; ?>

<?= $author_new_modal; ?>
