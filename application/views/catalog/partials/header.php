<!DOCTYPE html>

<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 ie"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 ie"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 ie"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9 ie"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js">
      </script>
    <![endif]-->
<head>
  <meta content="text/html; charset=utf-8" http-equiv="content-type">

  <title>LibriVox</title>
  <meta name="description" content="Librivox" />
  <meta name="author" content="Librivox" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="<?= base_url() ?>favicon.ico">
  <link rel="stylesheet" href="<?= base_url() ?>css/style.css?v=1">
  <link href='https://fonts.googleapis.com/css?family=Lato:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

  <script type="text/javascript" src="<?= base_url(); ?>js/catalog/small-menu.js"></script> 
 

  <script type="text/javascript">
  	var CI_ROOT = "<?= base_url(); ?>"
  </script>

</head>

<body>

	<section class="header-wrap">
		<header class="site-header">
		
			<!-- Site title/Logo and tagline -->
			<hgroup class="logo-wrap">
				<h1 class="logo"><a href="<?= base_url(); ?>"><img src="<?= base_url() ?>images/librivox-logo.png" alt="librivox-logo" width="180" height="37"><span class="assistive-text">Librivox</span></a></h1>
				<h3 class="tagline">Acoustical liberation of books in the public domain</h3>
			</hgroup>
			
			<!-- Sub menu -->
			<nav class="sub-menu">
			<h1 class="assistive-text icon-fontawesome-webfont"><span>Menu</span></h1>
			<div class="assistive-text skip-link"><a href="#" title="Skip to content">Skip to content</a></div> 
				<ul class="sub-menu-list">
					<li class="first"><a href="<?= base_url('pages/about-librivox/');?>">about</a></li>
					<li><a href="http://forum.librivox.org/">forum</a></li>	
					<li><a href="<?= base_url('pages/contact-librivox/');?>">contact</a></li>
					<li><a href="<?= base_url('pages/help/');?>">help</a></li>
					<li class="twitter ir"><a href="http://twitter.com/librivox">Twitter</a></li>
					<!-- <li class="facebook ir"><a href="http://www.facebook.com/LibriVox">Facebook</a></li> -->
					<li class="rss "><a href="<?= base_url('pages/librivox-feeds/');?>">rss</a></li> 
				</ul>																
			</nav><!-- end sub-menu -->   
			
			<!-- Search Form -->	
			<div class="search-wrap">
				<form role="search" action="#" id="searchform" method="get" class="searchform">
					<label class="assistive-text" for="q">Search Librivox</label>
					<input type="text" placeholder="Search by Author, Title or Reader" id="q" name="q" class="field">
					<input type="text" id="dummy" name="dummy" style="display:none;">
					<input type="submit" value="Search" id="searchsubmit" name="submit" class="submit">
				</form>

				<a href="#" class="advanced-search js-advanced-search"> Advanced search</a>	
			</div> <!-- end search-wrap -->		    
		</header>
		


	<!-- Main menu -->	
	<div class="main-menu-wrap">
		<section class="main-menu">
			<h3>Browse the catalog</h3>
			<nav class="main-menu-list-wrap"> 
				<ul class="main-menu-list">
					<li data-menu_item="author" class="js-menu_item first"><a href="#"><span class="author-icon"></span>Author</a></li>
					<li data-menu_item="title" class="js-menu_item last"><a href="#"><span class="title-icon"></span>Title</a></li>
					<li data-menu_item="genre" class="js-menu_item "><a href="#" ><span class="genre-icon"></span>Genre/Subject</a></li>
					<li data-menu_item="language" class="js-menu_item "><a href="#"><span class="language-icon"></span>Language</a></li>
					<!-- <li data-menu_item="reader" class="js-menu_item "><a href="#"><span class="reader-icon"></span>Reader</a></li> -->
					
				</ul>
			</nav>
			
													
		</section> <!-- end main-menu --> 
		

	</div> <!-- end .main-menu-wrap -->	  

	</section><!-- end .header-wrap -->	


	<input type="hidden" id="primary_key" value="<?= $primary_key; ?>">
