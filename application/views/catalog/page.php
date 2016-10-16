
<?= $header;  ?>

<div class="main-content">
	
	
	<?= $book_sidebar;  ?>
	
	
	<div class="page book-page">

			<div class="content-wrap clearfix">
				<div class="book-page-book-cover">
					<img src="<?= is_empty($project->coverart_jpg, base_url() . 'images/book-cover-150x150.gif') ?>" alt="book-cover-large" width="175" height="175" />

					<?php if (!empty($project->coverart_jpg)): ?>
					<a href="<?= $project->coverart_jpg ?>" class="download-cover">Download cover art</a>
					<?php endif; ?>

					<?php if (!empty($project->coverart_pdf)): ?>
					<a href="<?= $project->coverart_pdf ?>" class="download-cover">Download CD case insert</a>
					<?php endif; ?>

				</div>
				
				<h1><?= create_full_title($project)?></h1>
				
				<p class="book-page-author"><?= $authors_string ?></p>
				
				<div class="description"><?= $project->description ?></div>	<!-- .description -->

				<p class="book-page-genre"><span>Genre(s):</span> <?= $project->genre_list; ?></p>

				<p class="book-page-genre"><span>Language:</span> <?= $project->language; ?></p>

				<?php if(!empty($project->group)):?>
				<p class="book-page-genre"><span>Group:</span><a href="<?= base_url().'group/'. $project->group->group_id?>"> <?= $project->group->group_name; ?></a></p>
				<?php endif;?>

				
			</div> 	<!-- end .content-wrap --> 
		
			<table class="chapter-download">
					<thead>
						<tr>
							<th>Section</th>
							<th>Chapter</th>
							<?php if ($project->is_compilation):?>
								<th>Author</th>
								<th>Source</th>
							<?php endif; ?>

							<th>Reader</th>
							<th>Time</th>

							<?php if ($project->is_compilation):?>
								<th>Language</th>								
							<?php endif; ?>

						</tr>
					</thead>
						<tbody>
						<?php foreach ($sections as $section):  ?>	
							<tr>
								<td><a href="<?= $section->mp3_64_url ?>" class="play-btn">Play</a> <?= str_pad($section->section_number, 2, "0", STR_PAD_LEFT) ?> </td>
								<td><a href="<?= $section->mp3_128_url ?>" class="chapter-name"><?= $section->title?></a></td>
								<?php if ($project->is_compilation):?>
									<td><?= build_author_link($section->author)?></td>
									<td><a href="<?= $section->source?>">Etext</a></td>
								<?php endif; ?>

								<td><?= $section->reader?></td>
								<td><?= $section->time?></td>

								<?php if ($project->is_compilation):?>
									<td><?= get_language_code($section->language)?></td>									
								<?php endif; ?>
							</tr>

						<?php endforeach; ?>

					</tbody>
				</table>
	

</div><!-- end .page -->
</div><!-- end .main-content -->

<?= $footer;  ?>