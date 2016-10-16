<?php header("Content-Type: application/rss+xml; charset=utf-8"); ?>
<?php echo '<?xml version="1.0" ?>' ?>
<rss version="2.0">

<channel>

<title>LibriVox's New Releases</title>

<link>http://librivox.org</link>

<description>LibriVox volunteers record chapters of books in the public domain and release the audio files back onto the net. Our goal is to make all public domain books available as free audio books. We are a totally volunteer, open source, free content, public domain project.</description>


<!-- project loop -->

<?php foreach($projects as $project): ?>

	<?php $date_catalog = DateTime::createFromFormat('Y-m-d', $project->date_catalog); 	?>

<item>

<title><![CDATA[<?= $project->title_bar?>]]></title>
<description><![CDATA[<?= $project->description?>]]></description>

<link><![CDATA[<?= $project->url_librivox?>]]></link>
<guid><![CDATA[<?= $project->url_librivox?>]]></guid>

<pubDate><![CDATA[<?= $date_catalog->format('D, d M Y'); ?>]]></pubDate>

</item>

<?php endforeach; ?>

<!-- end file loop -->

</channel>

</rss>