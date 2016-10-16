<div id="profile_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="profile_label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="profile_label">Your Profile</h3>
	</div>

	<div class="modal-body">
		<div id="response_message" style="display:none;color:red;"></div>
		
           <form id="profile_form" method="post">              

            <input type="hidden" id="action" name="action" value="update">
            <input type="hidden" id="user_id" name="user_id">

            <?php $disabled = ($this->ion_auth->is_admin())? '': ' disabled '; ?>

             <div class="control-group">
                 <div class="controls ">
                        <label for="username" ><span class="span2"><span class="red" style="margin-right:4px;">*</span>User Name</span>
                        <input type="text" class="no-margin span3" name="username" id="username" <?= $disabled ?>>                        
                        </label>
                 </div>
             </div>
             
             <div class="control-group">
                 <div class="controls ">
                        <label for="display_name" ><span class="span2"><span class="red" style="margin-right:4px;">*</span> Display Name</span>
                        <input type="text" class="no-margin span3" name="display_name" id="display_name">                        
                        </label>
                 </div>
             </div>

             <div class="control-group">
                 <div class="controls ">
                        <label for="email"><span class="span2"><span class="red" style="margin-right:4px;">*</span> Email</span>
                        <input type="text" class="no-margin span3" name="email" id="email">                        
                        </label>
                 </div>
             </div>

             <div class="control-group">
                 <div class="controls ">
                        <label for="website"><span class="span2" ><span class="red" style="margin-right:8px;">&nbsp;</span>Website</span>
                        <input type="text" class="no-margin span3" name="website" id="website">                        
                        </label>
                 </div>
             </div>

             <div class="control-group">
                 <div class="controls ">
                        <label for="max_projects"><span class="span2"><span class="red" style="margin-right:4px;">*</span> Max Project Count</span>
                        <input type="text" class="no-margin span3" name="max_projects" id="max_projects">                        
                        </label>
                 </div>
             </div>

             <p id="password_label" style="margin-left: 40px;"><strong>Optional - if you'd like to change your password:</strong></p>

             <div class="control-group">
                 <div class="controls ">
                        <label for="password"><span class="span2" ><span class="red" style="margin-right:8px;">&nbsp;</span>Password</span>
                        <input type="password" class="no-margin span3" name="password" id="password">                        
                        </label>
                 </div>
             </div>

             <div class="control-group">
                 <div class="controls ">
                        <label for="confirm_password"><span class="span2" ><span class="red" style="margin-right:8px;">&nbsp;</span>Confirm Password</span>
                        <input type="password" class="no-margin span3" name="confirm_password" id="confirm_password">                        
                        </label>
                 </div>
             </div>     

             <div id="groups_block">

                <p id="groups_label" style="margin-left: 40px;"><strong>Add Roles:</strong></p>

                <?php foreach ($roles as $role): ?>
                 <div class="control-group">
                     <div class="controls ">
                            <label for="groups"><span class="span2" ><span class="red" style="margin-right:8px;">&nbsp;</span><?= $role['label'];?></span>
                            <input type="checkbox" class="group_box" name="groups[]" id="groups_<?= $role['value'];?>" value="<?= $role['value'];?>" <?= ($role['checked']) ? 'checked':''; ?>>                        
                            </label>
                     </div>
                 </div> 
                <?php endforeach; ?> 
                <input type="hidden" class="group_box" name="groups[]" id="groups_0" value="0">

             </div>  


           </form> 		



	</div>

	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button id="edit_profile_submit" class="btn btn-primary">Save changes</button>
	</div>
</div>