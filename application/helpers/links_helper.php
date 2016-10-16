<?php

// build various links for rss, torrents, iarchive, etc

function torrent_link($url_iarchive = '')
{
	if (empty($url_iarchive)) return '';

	// sample link: http://www.archive.org/details/woman_as_decoration_0910_librivox
	// sample torrent: http://www.archive.org/download/woman_as_decoration_0910_librivox/woman_as_decoration_0910_librivox_archive.torrent

	$archive_torrent_link = "http://www.archive.org/download/";

	$url_iarchive = str_replace('www.', '', $url_iarchive);
	$url_iarchive = str_replace('http://archive.org/details/', '', $url_iarchive);
	$torrent_ext = '_archive.torrent';

	if (strlen(trim($url_iarchive)) == 0) return '';

	return $archive_torrent_link . $url_iarchive . '/' . $url_iarchive . $torrent_ext;

}

function rss_link()
{
	//http://librivox.org/rss/6792


}

function itunes_subscribe_link()
{
	//itpc://librivox.org/rss/6792

}