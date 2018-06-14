<li class="catalog-result">

	<?php if ($this->uri->segment(1) == 'advanced_search'): ?>
		<div class="catalog-type"><span class="author-icon"></span>Author</div>
	<?php endif; ?>

	<div class="result-data">
		<h3><a href="<?= base_url('author/'. $item['author_id']) ?>"><?= build_author_name((object) $item)?> <span class="dod-dob"><?= build_author_years((object) $item) ?></span></a></h3>
		<p class="book-meta">
			<?php if ($item['meta_complete'] > 0): ?>
				Completed: <span><?= translate_plural("%d book", "%d books", $item['meta_complete']) ?></span>
			<?php endif; ?>
			<?= $item['meta_complete'] > 0 && $item['meta_in_progress'] > 0 ? '|' : '' ?>
			<?php if ($item['meta_in_progress'] > 0): ?>
				In progress: <span><?= translate_plural("%d book", "%d books", $item['meta_in_progress']) ?></span>
			<?php endif; ?>
		</p>
	</div>	
	<a href="<?= base_url('author/'. $item['author_id']) ?>" class="more-result">more results</a>		
</li>