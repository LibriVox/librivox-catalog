<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
	
		<!-- Head includes the page title and description -->
		<?php echo $head; ?>
		
		<!-- Mobile viewport optimized: h5bp.com/viewport -->
		<meta name="viewport" content="width=device-width">
		
		<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

		<script type="text/javascript">
		    CI_ROOT = "<?=base_url() ?>";
		</script>
		
		<!-- styles will echo with style tags -->
		<?= $_styles; ?>
		
		
	</head>
	
	<body>
		<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
	       chromium.org/developers/how-tos/chrome-frame-getting-started -->
	  	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
		
		<?php echo $header; ?>
		
		<?= isset($dashboard)? $dashboard: ''; ?>
		
		<div id="wrapper" class="container">
			
			<div id="content" class="row">
				<div id="content_left" class="span12">                
				<?php echo $content_left; ?>
				</div>
				<div id="content_right" class="span3">                
				<?php echo $content_right; ?>
				</div>
			</div>
			

		
     		<footer>             
     			<?php echo $bottom_tagline; ?>
     			
     			<?php echo $footer; ?>
     		</footer>

			
		</div>	
	
		<!-- scripts will echo with script tags -->
		<?= $_scripts; ?>
		
	</body>

</html>
