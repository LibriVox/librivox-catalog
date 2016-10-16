<div class="hero-unit">

      <div class="pageTitleBorder"></div>

      <h3>Change Password</h3>

      <div id="infoMessage"><?php echo $message;?></div>

      <?php echo form_open("auth/change_password");?>

            <p>Old Password:<br />
            <?php echo form_input($old_password);?>
            </p>
            
            <p>New Password (at least <?php echo $min_password_length;?> characters long):<br />
            <?php echo form_input($new_password);?>
            </p>
            
            <p>Confirm New Password:<br />
            <?php echo form_input($new_password_confirm);?>
            </p>
            
            <?php echo form_input($user_id);?>
            <p><?php echo form_submit('submit', 'Change');?></p>
            
      <?php echo form_close();?>

</div>