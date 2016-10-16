<?= $header;  ?>


<div class="main-content">
	
	
	<?= $sidebar;  ?>
	
	
	<div class="page author-page">
		<div class="page-header-wrap js-header_section">
			<div class="content-wrap clearfix">
				<h1><?= $reader['display_name'] ?></h1>
				
				<div class="page-header-half">
					<p><span>Catalog name:</span> <?= $reader['display_name'] ?> </p>
					<p><span>Forum name:</span> <?= $reader['username'] ?></p>
					<p><a href="<?= base_url('sections/readers/'.$reader['id']) ?>">Reader section details</a></p>					
				</div>
				
				<div class="page-header-half">	
					<p><span>Total sections:</span> <?= $sections ?></p>
					<p><span>Total matches:</span> <?= $matches ?></p>
					<p><span>URL:</span> <a href="<?= $reader['website'] ?>"><?= $reader['website'] ?></a></p>
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