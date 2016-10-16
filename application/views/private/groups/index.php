
<?= $menu_header; ?>

<div class="" >
	<div class="pull-left">
		<h4 class="toggle_form_btn" data-toggle_div_id="group_box_new" style="cursor:pointer;">New Group</h4>
			<div id="group_box_new" style="display:none;">
				<form>
					<div class="control-group">
                     <div class="controls center">
                     	<input type="hidden" name="id" value="0">
						<label>Name:</label> <input  name="name"class="span8" />
						<label>Description:</label> <input  name="description" class="span8" />
						<label>Add project:</label> <input  name="add_project" class="span8"  placeholder="Add comma-separated project id's, eg. 123, 124, 125"/>
					 </div>
					</div>

				    <input class="update_group btn span1 pull-right" value="Add"/>
				</form>

			</div>


		<h4>Group List</h4>

		<?php foreach ($groups as $group): ?>
			<div id="group_<?= $group->id?>">
				<h5 class="toggle_form_btn" data-toggle_div_id="group_box_<?= $group->id?>" style="cursor:pointer;"><?= $group->name?></h5>
				<div class="group_box" id="group_box_<?= $group->id?>" style="display:none;">
					<form method="post">
						<div class="control-group">
                         <div class="controls center">
                         	<input type="hidden" id="group_id_<?= $group->id?>" name="id" value="<?= $group->id?>">
							<label>Name:</label> <input id="group_name_<?= $group->id?>" name="name"class="span8" value="<?= $group->name ?>"/>
							<label>Description:</label> <input id="group_description_<?= $group->id?>" name="description" class="span8" value="<?= $group->description ?>"/>
							<label>Add project:</label> <input id="group_add_project_<?= $group->id?>" name="add_project" class="span8" value="" placeholder="Add comma-separated project id's, eg. 123, 124, 125"/>
						 </div>
						</div>
					<input type="button" id="group_delete_<?= $group->id?>" data-id="<?= $group->id?>" class="delete_group btn btn-tiny btn-danger pull-right" style="margin-left:4px;" value="Delete Group"/>
					<input type="button" id="group_update_<?= $group->id?>" data-id="<?= $group->id?>" class="update_group btn btn-tiny pull-right" value="Update"/>

					</form>

					<p>Project list:</p>
					<?php foreach ($group->projects as $project): ?>
						<div><i class="icon-remove delete_project" style="width:20px;cursor:pointer;" data-group_id="<?= $group->id?>" data-project_id="<?= $project['id']?>"></i><?= $project['id'] . ' - ' . $project['title'] ?></div>

					<?php endforeach; ?>

				</div>

				<hr>
			</div>

		<?php endforeach; ?>
	</div>	
</div>