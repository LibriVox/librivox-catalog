<div class="hero-unit">

	<div class="pageTitleBorder"></div>
	<p>Please login with your username and password below.</p>
	
	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("auth/login");?>
    	
      <div class="control-group">
        <div class="controls center">
      	 <label for="identity">Username:</label>
      	 <?php echo form_input($identity);?>
        </div>
      </div>
      
      <div class="control-group">
        <div class="controls center">
      	<label for="password">Password:</label>
      	<?php echo form_input($password);?>
        </div>
      </div>
      
      <div class="control-group">
        <div class="controls center">
	      <label for="remember">Remember Me:
	      <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
        <a href="<?= base_url()?>auth/forgot_password" class="" style="margin-left:20px;"> Forgot password</a>

        </label>
        </div>
      </div>

      <input type="hidden" name="redirect_url" id="redirect_url" value="<?= $redirect_url ?>"/>
      
      
        <div class="control-group">
            <div class="controls center" style="margin-left: 150px;">
                <button class="btn btn-large btn-primary">Login</button>
            </div>
        </div>      

      
    <?php echo form_close();?>


</div>
