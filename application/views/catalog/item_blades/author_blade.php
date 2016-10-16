<li class="catalog-result">

	<?php if ($this->uri->segment(1) == 'advanced_search'): ?>
		<div class="catalog-type"><span class="author-icon"></span>Author</div>
	<?php endif; ?>

	<div class="result-data">
		<h3><a href="<?= base_url('author/'. $item['author_id']) ?>"><?= build_author_name((object) $item)?> <span class="dod-dob"><?= build_author_years((object) $item) ?></span></a></h3>
		<p class="book-meta">Completed: <span><?= $item['meta_complete']?> books</span> | In progress: <span><?= $item['meta_in_progress']?> books</span></p>
	</div>	
	<a href="<?= base_url('author/'. $item['author_id']) ?>" class="more-result">more results</a>		
</li>