<li class="catalog-result">

	<?php if ($this->uri->segment(1) == 'advanced_search'): ?>
		<div class="catalog-type"><span class="title-icon"></span>Title</div>
	<?php endif; ?>

	<?php
		if ($item['status'] == PROJECT_STATUS_COMPLETE)
		{
			if (empty($item['coverart_thumbnail']))
				$image = base_url() . 'images/book-cover-default-65x65.gif';
			else
				$image = $item['coverart_thumbnail'];

			$url = $item['url_librivox'];
		}
		else
		{
			$image = base_url() . 'images/book-cover-in-progress-65x65.gif';
			$url = $item['url_forum'];
		}
	?>

	<a href="<?= $url ?>" class="book-cover"><img src="<?= $image ?>" alt="book-cover-65x65" width="65" height="65" /></a>

	<?php 
		if (!empty($item['author_list']))
		{
			$authors = $item['author_list'];
		} 
		else
		{
			$authors = '<a href="'. base_url('author/'. $item['author_id']) .' ">'. build_author_name((object) $item) .' <span class="dod-dob">' . build_author_years((object) $item) .'</span></a>';
		}	

	?>


	<div class="result-data">
		<?php if ( isset($item['source_table']) &&  $item['source_table'] == 'sections'): ?>
			<h3><?= $item['search_field']?> (in <a href="<?= $url ?>"> <?= create_full_title($item)?></a>)</h3>
		<?php else: ?>
			<h3><a href="<?= $url ?>"><?= create_full_title($item)?></a></h3>
		<?php endif; ?>

		<p class="book-author"> <?= $authors ?> </p>
		<p class="book-meta"> <?= str_replace('_', ' ', ucwords($item['status']))?> | <?= ucwords($item['project_type'])?> | <?= $item['language']?></p>
	</div>	

	<?php if ($item['status'] == 'complete'): ?>
	<div class="download-btn">
		<a href="<?= $item['zip_url']?>">Download</a>
		<span><?= $item['zip_size']?></span>
	</div>
	<?php endif; ?>	
</li>
