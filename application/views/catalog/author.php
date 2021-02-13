<?= $header;  ?>


<div class="main-content">
	
	
	<?= $sidebar;  ?>
	
	
	<div class="page author-page">
		<div class="page-header-wrap js-header_section">
			<div class="content-wrap clearfix">
				<h1><?= format_author($author, FMT_AUTH_YEARS|FMT_AUTH_HTML)?></h1>
				
				<p class="description"><?= $author->blurb ?></p>
				
				<div class="page-header-half">
					<p><span>External Links</span></p>
					<?php 
						if (!empty($author->author_url))
						{
							echo '<p>' . format_author($author, FMT_AUTH_WIKI) . '</p>';
						}	
					 	
					 ?>				 
				</div>
				
				<div class="page-header-half">	
					<p><span>Total matches:</span> <?= $matches ?></p>
				</div>	

			</div>	
		
			
			<div class="sort-menu" id="sort_menu" style="display:none;">
				<p>Order by</p>
				<select class="js-sort-menu">
					  <option value="alpha">Alphabetically</option>
					  <option value="catalog_date">Release date</option>
				 </select> 
			</div><!-- end .sort-menu -->
		</div><!-- end . page-header -->
			 		
		<ul class="browse-list">

					
			
		</ul>
	
	<div class="page-number-nav"></div>
	
	</div>
</div><!-- end .page -->

</div><!-- end .main-content -->

<?= $footer;  ?>