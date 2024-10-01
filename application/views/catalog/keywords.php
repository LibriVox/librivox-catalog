<?= $header;  ?>

<div class="main-content advanced-search-form">
		
	<div id="sidebar_wrapper" >
		<?= $sidebar;  ?>
	</div>
	
	
	<div class="browse browse-title">
		<div class="browse-header-wrap">
			<h4 class="browse-header"></h4>
			<h3>Audiobooks tagged with keywords "<?= $keywords->value ?>"</h3>

			<div class="sort-menu" id="sort_menu" style="display:none;">
				<p>Order by</p>
				<select class="js-sort-menu">
					<option value="alpha" <?= $search_order === 'alpha' ? 'selected' : '' ?>>Alphabetically</option>
					<option value="catalog_date" <?= $search_order === 'catalog_date' ? 'selected' : '' ?>>Release date</option>
				</select> 
			</div><!-- end .sort-menu -->
			
		</div>

		<ul class="browse-list"></ul>
	
		<div class="page-number-nav"></div>
	
	</div>


</div><!-- end .main-content -->

<?= $footer;  ?>



</body>
</html>
