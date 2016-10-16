<li class="catalog-result">
	<div class="result-data">
		<h3><a href="#" class="js-sublink" data-sub_category="language" data-primary_key="<?= $item['id'] ?>"><?= $item['language']?></a></h3>

		<?php if (strtolower($item['native']) != 'english'): ?>
			<p class="native-lang"><?= $item['native']?></p>
		<?php endif; ?>

				<p class="book-meta">Completed: <span><?= $item['meta_complete']?> books</span> | In progress: <span><?= $item['meta_in_progress']?> books</span></p>
	</div>
	<a href="#" data-sub_category="genre" data-primary_key="<?= $item['id'] ?>" class="js-sublink more-result">more results</a>		
</li>