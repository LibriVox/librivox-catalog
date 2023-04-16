<?php header("Content-Type: application/rss+xml; charset=utf-8"); ?>
<?php echo '<?xml version="1.0" ?>' ?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:media="http://search.yahoo.com/mrss/" xmlns:creativeCommons="http://backend.userland.com/creativeCommonsRssModule" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
<channel>
  <title><![CDATA[<?= $project->title_bar ?>]]></title>
  <link><![CDATA[<?= $project->url_librivox ?>]]></link>
  <atom:link rel="self" href="https://librivox.org/rss/<?= $project->id ?>" />
  <description><![CDATA[<?= strip_tags($project->description) ?>]]></description>
  <!--<genre>project element=Genre</genre>-->
  <!--<language>project element=lang.code</language>-->
  <itunes:type>serial</itunes:type>
  <itunes:author>LibriVox</itunes:author>
  <itunes:summary><![CDATA[<?= strip_tags($project->description) ?>]]></itunes:summary>
  <itunes:owner>
    <itunes:name>LibriVox</itunes:name>
    <itunes:email>info@librivox.org</itunes:email>
  </itunes:owner>
  <itunes:category text="Arts">
    <itunes:category text="Literature" />
  </itunes:category>
  <?php
  if ($project->coverart_jpg) {
    echo sprintf('<itunes:image href="%s" />', $project->coverart_jpg);
  }
  ?>
  <!-- file loop -->
  <?php foreach($sections as $section): ?>
  <item>
    <title><![CDATA[<?= $section->title?>]]></title>
    <itunes:episode><![CDATA[<?= $section->section_number?>]]></itunes:episode>
    <!--<reader>file element=reader</reader> -->
    <link><![CDATA[<?=  $project->url_librivox?>]]></link>
      <?php /* 1 kbps is equivalent to 125 bytes per second. Our 64kbps
               bitrate is therefore equivalent to 8000 bytes per second. We
               could therefore use playtime * 8000 to calculate the enclosure
               length in bytes as the standard demands. However, that doesn't
               give us the exact file size in bytes. In case of unknown
               enclosure length, the W3C recommends we use 0 [1], so that's
               what we do.

               [1] https://validator.w3.org/feed/docs/error/UseZeroForUnknown.html */ ?>
      <enclosure url="<?= $section->mp3_64_url?>" length="0" type="audio/mpeg" />
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
