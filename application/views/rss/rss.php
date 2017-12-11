<?php header("Content-Type: application/rss+xml; charset=utf-8"); ?>
<?php echo '<?xml version="1.0" ?>' ?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:media="http://search.yahoo.com/mrss/" xmlns:creativeCommons="http://blogs.law.harvard.edu/tech/creativeCommonsRssModule" version="2.0">
<channel>
  <title><![CDATA[<?= $project->title_bar ?>]]></title>
  <link><![CDATA[<?= $project->url_librivox ?>]]></link>
  <description><![CDATA[<?= $project->description ?>]]></description>
  <!--<genre>project element=Genre</genre>-->
  <!--<language>project element=lang.code</language>-->
  <itunes:author>LibriVox</itunes:author>
  <itunes:summary><![CDATA[<?= $project->description ?>]]></itunes:summary>
  <itunes:owner>
    <itunes:name>LibriVox</itunes:name>
    <itunes:email>info@librivox.org</itunes:email>
  </itunes:owner>
  <itunes:category text="Arts">
    <itunes:category text="Literature" />
  </itunes:category>
  <!-- file loop -->
  <?php foreach($sections as $section): ?>
  <item>
    <title><![CDATA[<?= $section->title?>]]></title>
    <!--<reader>file element=reader</reader> -->
    <link><![CDATA[<?=  $project->url_librivox?>]]></link>
    <enclosure url="<?= $section->mp3_64_url?>" type="audio/mpeg" />
  <itunes:explicit>No</itunes:explicit>
  <itunes:block>No</itunes:block>
  <itunes:duration><![CDATA[<?= $section->migrated_time?>]]></itunes:duration>
  <!--<pubDate>file element=rss.pubDate</pubDate>-->
  <media:content url="<?= $section->mp3_64_url?>" type="audio/mpeg" />
 </item>
  <?php endforeach; ?>
  <!-- end file loop -->
</channel>
</rss>
