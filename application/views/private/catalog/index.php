Catalog check page: <br/>
===================

<?php 
	if (!empty($message))
	{
		echo  "<br/>".$message;
	}

?>


<h4><?= trim($project->title_prefix. ' ' . $project->title); ?></h4>

<p>by <a href="<?= $author_link ?>"><?= $authors[0]['first_name'] . ' ' . $authors[0]['last_name'] ?></a> </p>

<p><?= $project->description; ?></p>

<p> <a href="<?= $project->url_text_source; ?>">Online text</a> </p>
<p> <a href="<?= $authors[0]['author_url']; ?>">Wikipedia – <?= $authors[0]['first_name'] . ' ' . $authors[0]['last_name'] ?></a> </p>
<p> <a href="<?= $project->url_other; ?>">Wikipedia – <?= $project->title_prefix. ' ' . $project->title; ?></a> </p>
<p> <a href="<?= $project->link_mb4; ?>">M4B audiobook of Complete Book</a> </p>
<p> <a href="<?= $project->url_iarchive; ?>"><?= $project->text_iarchive; ?></a> </p>
<p> <a href="<?= 'http://www.archive.org/download/tom_sawyer_librivox/tom_sawyer_librivox_64kb_mp3.zip'; ?>">Zip file of the entire book (195.1MB) (6:46:12)</a> </p>
<p> <a href="<?= 'http://librivox.org/bookfeeds/tom-sawyer-by-mark-twain.xml'; ?>">RSS feed</a> </p>
<p> <a href="<?= 'itpc://librivox.org/bookfeeds/tom-sawyer-by-mark-twain.xml'; ?>">Subscribe in iTunes</a> </p>
<p> <a href="<?= 'http://www.archive.org/details/librivox_cd_covers'; ?>">CD Cover and Album Art</a> </p>