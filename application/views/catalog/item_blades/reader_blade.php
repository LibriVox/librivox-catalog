<li class="catalog-result">

	<?php if ($this->uri->segment(1) == 'advanced_search'): ?>
		<div class="catalog-type"><span class="reader-icon"></span>Reader</div>
	<?php endif; ?>

	<div class="result-data">
		<h3><a href="<?= base_url('reader/'. $item['reader_id']) ?>"><?= $item['display_name'] ?></a></h3>
		<h3><a href="<?= base_url('reader/'. $item['reader_id']) ?>"><?= $item['username'] ?></a></h3>	
	</div>		
	<a href="<?= base_url('reader/'. $item['reader_id']) ?>" class="more-result">more results</a>		
</li>