<div class="sidebar book-page">


	<a href="<?= base_url(); ?>pages/donate-to-librivox/" class="donate">Donate to Librivox</a>

	<a href="<?= base_url(); ?>pages/thank-a-reader/" class="thank-reader">Thank a reader</a>


	
	<div class="disclaimer">
		LibriVox recordings are Public Domain in the USA. If you are not in the USA, please verify the copyright status of these works in your own country before downloading, otherwise you may be violating copyright laws.
	</div>


	
	<div class="book-page-sidebar">
		
		<h4>Listen/Download (<a href="<?= base_url('pages/help/') ?>">help?</a>)</h4>

		<dl class="listen-download clearfix">
			<?php if(!empty($project->zip_url)): ?>	
			  <dt>Whole book (zip file)</dt>
			  <dd><a href="<?= $project->zip_url ?>" class="book-download-btn">Download</a></dd>
			<?php endif;?>

			  <dt>Subscribe by iTunes</dt>
			  <dd><a href="itpc://librivox.org/rss/<?= $project->id ?>" class="book-download-btn">iTunes</a></dd>

			  <dt>RSS Feed</dt>
			  <dd><a href="http://librivox.org/rss/<?= $project->id ?>" class="book-download-btn">RSS</a></dd>

			  <dt>Download torrent</dt>
			  <dd><a href="<?= torrent_link($project->url_iarchive) ?>" class="book-download-btn">Torrent</a></dd> 					  
		</dl>

		
					
	</div>
	
	<div class="book-page-sidebar">
		<h4>Production details</h4>
		<dl class="product-details clearfix">
		  <dt>Running Time:</dt>
		  <dd><?= is_empty($project->totaltime, '&nbsp;');?></dd>

		  <dt>Zip file size:</dt>
		  <dd><?= is_empty($project->zip_size, '&nbsp;');?></dd>
		
		  <dt>Catalog date:</dt>
		  <dd><?= is_empty($project->date_catalog, '&nbsp;');?></dd>
		
		  <dt>Read by:</dt>
		  <dd><?= is_empty($project->read_by, '&nbsp;');?></dd> 
		  
		  <dt>Book Coordinator:</dt>
		  <dd><a href="<?= base_url().'reader/'. is_empty_object($volunteers->bc, 'id', ''); ?>"><?= is_empty_object($volunteers->bc, 'display_name', '&nbsp;')?></a></dd>
		  
		  <dt>Meta Coordinator:</dt>
		  <dd><a href="<?=  base_url().'reader/'. is_empty_object($volunteers->mc, 'id', ''); ?>"><?= is_empty_object($volunteers->mc, 'display_name', '&nbsp;')?></a></dd>
		  
		  <dt>Proof Listener:</dt>
		  <dd><a href="<?= base_url().'reader/'. is_empty_object($volunteers->pl, 'id', '') ?>"><?= is_empty_object($volunteers->pl, 'display_name', '&nbsp;')?></a></dd>				  
		</dl>

	</div>
	
	<div class="book-page-sidebar">
		<h4>Links</h4>

		<?php if (!empty($project->url_iarchive)): ?>
			<p><a href="<?= $project->url_iarchive ?>">Internet Archive Page</a></p>
		<?php endif; ?>

		<?php if (!empty($project->url_text_source)): ?>
			<p><a href="<?= $project->url_text_source ?>">Online text</a></p>
		<?php endif; ?>

		<?php if(!empty($authors)): foreach($authors as $author):?>
			<?php if (!empty($author['author_url'])): ?>
				<p><a href="<?= $author['author_url'] ?>">Wikipedia - <?= $author['first_name'] . ' ' . $author['last_name'] ?></a></p>
				<?php endif; ?>
		<?php endforeach; endif; ?>	

		<?php if (!empty($project->url_project)): ?>
			<p><a href="<?= $project->url_project ?>">Wikipedia Book - <?= create_full_title($project)?></a></p>
		<?php endif; ?>

		<?php if (!empty($project->project_urls)): foreach ($project->project_urls as $project_url): ?>
			<p><a href="<?= $project_url->url ?>"><?= $project_url->label?></a></p>
		<?php endforeach; endif; ?>

	</div>
				




</div>