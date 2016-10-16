<li class="catalog-result all">
	<div class="result-data">
		<h3><a href="#" class="js-sublink" data-sub_category="genre" data-primary_key="<?= $item['id'] ?>"><?= $item['full_path']?></a></h3>
		<p class="book-meta">Completed: <span><?= $item['meta_complete']?> books</span> | In progress: <span><?= $item['meta_in_progress']?> books</span></p>
	</div>
	<a href="#" data-sub_category="genre" data-primary_key="<?= $item['id'] ?>" class="js-sublink more-result">more results</a>		
</li>
