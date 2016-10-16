<?= $menu_header; ?>


<div class="" >
	<div class="pull-left">

	<h4>Language Manager</h4><button class="btn btn-small btn-primary toggle_form_btn" style="margin-bottom:10px;" data-toggle_div_id="add_new_language">Add new</button>

	<div id="add_new_language" style="display:none;margin-bottom:10px;">
		<form id="new_language_form">
			<table>
				<thead>
					<tr>
						<th>Language</th><th>Native</th><th>2-letter Code</th><th>3-letter Code</th>
					</tr>
				</thead>			

				<tbody>
					<tr>
						<td><input name="language"  style="width:250px;"/></td>
						<td><input name="native"  style="width:250px;"/></td>
						<td><input name="two_letter_code"  style="width:100px;"/></td>
						<td><input name="three_letter_code"  style="width:100px;"/></td>
						<td><button class="btn btn-small btn-primary save_language" >Save</button></td>
					</tr>
				</tbody>
			</table>
		</form>

	</div>


	<table class="table table-bordered" id="languages_table">
		<thead>
			<tr> 
				<th>Id</th> <th>Language</th> <th>Active</th> <th>Common</th> <th>2-letter Code</th> <th>3-letter Code</th> <th>Native</th> 
			</tr>
		</thead>

		<tbody>
			<?php foreach ($languages as $language): ?>

				<?php 
					if ($language->active)
					{
						$active_class = 'btn-success';
						$active_label = 'Yes';			
					}
					else
					{
						$active_class = '';
						$active_label = 'No';						
					}	

					if ($language->common)
					{
						$common_class = 'btn-success';
						$common_label = 'Yes';			
					}
					else
					{
						$common_class = '';
						$common_label = 'No';						
					}				

					
				?>

				<tr data-language_id="<?= $language->id;?>">

					<td><?= $language->id;?></td> 

					<td id="language-<?= $language->id ?>" class="edit" ><?= $language->language;?></td>

					<td><span class="toggle_language btn <?= $active_class ?>" id="active-<?= $language->id ?>" data-status="<?= $language->active ?>" ><?= $active_label ?></span></td>

					<td><span class="toggle_language btn <?= $common_class ?>" id="common-<?= $language->id ?>" data-status="<?= $language->common ?>" ><?= $common_label ?></span></td>

					<td id="two_letter_code-<?= $language->id ?>" class="edit" ><?= empty($language->two_letter_code)? '': $language->two_letter_code;?></td>

					<td id="three_letter_code-<?= $language->id ?>" class="edit" ><?= empty($language->three_letter_code)? '': $language->three_letter_code;?></td>

					<td id="native-<?= $language->id ?>" class="edit" ><?= $language->native;?></td>
				</tr>

			<?php endforeach; ?>
		</tbody>		
	</table>
	</div>

	<div class="pull-left span1"></div>


</div>

<div style="height:100px;"></div>