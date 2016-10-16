<div class="hero-unit">

	<div class="pageTitleBorder"></div>
	
	<h3>Forgot Password</h3>
	<p>Please enter your username so we can send you an email to reset your password.</p>

	<div id="infoMessage"><?php echo $message;?></div>

	<?php echo form_open("auth/forgot_password");?>

	      <p>Username:<br />
	      <?php echo form_input($email);?>
	      </p>
	      

	      <button class="btn btn-large btn-primary">Submit</button>
	      
	<?php echo form_close();?>

</div>