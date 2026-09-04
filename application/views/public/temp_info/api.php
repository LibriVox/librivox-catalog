<h4>API Info</h4>
===============


<p>The API is currently in: released</p>
<p>Last update (approximate go-live date: 2026-09-26) - Added a maximum of 500 to the "limit" parameter.</p>

<p>DEVELOPERS: have a look at the <a href="#dev_notes">Dev Notes</a> 
	area at the bottom of the page for a notes on possible future changes.</p>

<div style="height:10px;"/></div>

<h4>Audiobooks API</h4>

<p>Endpoint: <a href="<?= base_url()?>api/feed/audiobooks"><?= base_url()?>api/feed/audiobooks</a></p>

<p>Parameters:</p>

<ul>
	<li>id - fetches a single record</li>
	<li>since - takes a UNIX timestamp; returns only the projects cataloged since that date</li>
	<li>author - all projects with matching authors</li>
	<li>title - all projects with matching titles</li>
	<li>genre - all projects with matching genres</li>
	<li>fields - if set, will return ONLY the listed fields</li>
	<li>extended - if set to 1, will include certain optional fields (see "List of return fields")</li>
	<li>coverart - if set to 1, will include optional fields related to cover-art</li>
	<li>limit - will cap the number of results you receive.  Defaults to 50.  Can no longer be set above <?= API_RESULT_MAX ?>.</li>
	<li>offset - if set, will skip this many of the first results, allowing you to access results beyond that limit</li>
	<li>format - how the server will format results for you</li>
	<li>sort_order - the ordering of results, as sorted by project id.  Either 'asc' (smallest IDs first, the default) or 'desc' (the opposite).</li>
</ul>

<h5>Search and wild-cards</h5>
<p>Note that the title, author & genre may be searched on with ^ as before, to anchor the beginning of the search term.</p>
Example - all books with titles beginning with "best":<br>
<a href="<?= base_url()?>api/feed/audiobooks/title/^best"><?= base_url()?>api/feed/audiobooks/title/^best</a>

<h5>Parameters and URL formatting</h5>
<p>Parameters do not have to appear in '?param=value' format, but can also appear as paths ('/param/value').</p>
Valid examples:<br>
<a href="<?= base_url()?>api/feed/audiobooks/?id=52&format=xml"><?= base_url()?>api/feed/audiobooks/?id=52&format=xml</a><br>
or<br>
<a href="<?= base_url()?>api/feed/audiobooks/id/52/format/xml"><?= base_url()?>api/feed/audiobooks/id/52/format/xml</a><br>

<h5>Large-dataset queries</h5>
<p>
	Note that 'limit' and 'offset' work similarly to those statements in SQL.  If you need more than <?= API_RESULT_MAX ?> results, you can send multiple
	requests, using 'offset' to skip the ones you've previously accessed.  If you intend to access a large number of records this way, please be
	considerate, and add a delay between your requests.
</p>
Example - the first three books beginning with "best", and then the next three:<br>
<a href="<?= base_url()?>api/feed/audiobooks/title/^best?limit=3"><?= base_url()?>api/feed/audiobooks/title/^best?limit=3</a><br>
<a href="<?= base_url()?>api/feed/audiobooks/title/^best?limit=3&offset=3"><?= base_url()?>api/feed/audiobooks/title/^best?limit=3&offset=3</a></p>

<h5 id="return_formats">Return formats</h5>
<p>The default format is xml, but also currently available are 'json', 'jsonp', 'serialized', and 'php' ('array()' format)</p>
Example:<br>
<a href="<?= base_url()?>api/feed/audiobooks/?id=52&format=json"><?= base_url()?>api/feed/audiobooks/?id=52&format=json</a>

<h5>Requesting specific fields:</h5>

<p>
	You can request for only specific fields will be returned in the following two ways:<br>
	&fields={id,title,authors,url_rss}<br>
	or<br>
	&fields[]=id&fields[]=title&&fields[]=authors&fields[]=url_rss
</p>

<p>This supports sub-groups (authors, translators, sections, genres) but not fields within the subgroups (yet)</p>

<h5>List of available return fields:</h5>
<p>* indicates a field that is not included by default.</p>
<ul>
	<li>id - the LibriVox ID number for this recording project</li>
	<li>title - the title of the work</li>
	<li>description - a blurb by the coordinator or reader</li>
	<li>url_text_source - a link to the source text for this recording</li>
	<li>language - the English display name for the language of the reading (e.g. "English", "French", "Multilingual")</li>
	<li>copyright_year - the year of copyright on the specific source text used for this recording</li>
	<li>num_sections - the count of separate audio files (often corresponds to chapters)</li>
	<li>url_rss - the URL of a feed on the LibriVox web site, where this reading can be accessed as a podcast</li>
	<li>url_zip_file - the URL of a .zip file containing the all of the audio files.  Hosted by Archive.org.</li>
	<li>url_project - the URL of the working forum thread, if still in progress</li>
	<li>url_librivox - the URL of the LibriVox catalog page for this project</li>
	<li>url_iarchive* - the URL of the Archive.org page for this project's files.  Included with 'extended=1'</li>
	<li>url_other - an object/array of other URLs related to the project, if applicable.  Additional sources or reference materials.</li>
	<li>totaltime - formatted total playback time</li>
	<li>totaltimesecs - integer total playback time</li>
	<li>authors - an object/array with information about each author of the text (pseudo-author "Various" is used for certain projects, such as Collections)</li>
	<li>sections* - an object/array with information about each audio file recorded.  Included with 'extended=1'</li>
	<li>genres* - an object/array with the names of genres provided for this project.  Included with 'extended=1'</li>
	<li>translators* - an object/array with information about each translator of the text (if relevant).  Included with 'extended=1'</li>
	<li>coverart_jpg* - the URL of a cover image for this project.  Included with 'coverart=1'</li>
	<li>coverart_pdf* - the URL of a printable CD cover insert for this project, with track and runtime information.  Included with 'coverart=1'</li>
	<li>coverart_thumbnail* - the URL of a thumbnail version of coverart_jpg.  Included with 'coverart=1'</li>
</ul>
<div style="height:30px;"></div>

<h4>Simple Audiotracks API</h4>

<p>Endpoint: <?= base_url()?>api/feed/audiotracks</p>

<p>Parameters:</p>

<ul>
	<li>id - of track itself</li>
	<li>project_id - all tracks for project</li>
	<li>format - how the server will format results for you (<a href="#return_formats">same as above</a>)</li>
</ul>

<div style="height:30px;"></div>

<h4>Simple Authors API</h4>

<p>Endpoint: <?= base_url()?>api/feed/authors</p>

<p>Parameters:</p>

<ul>
	<li>id - of author</li>
	<li>last_name - exact match</li>
	<li>format - how the server will format results for you (<a href="#return_formats">same as above</a>)</li>
</ul>

<div style="height:30px;"></div>

<div id="dev_notes"><h4>Dev Notes</h4></div>

<p>
	Here is a TODO list of what has been requested and/or worked on.  For discussion with other API users, or if you have questions, please
	join us in the <a href="https://forum.librivox.org/viewtopic.php?t=44129">LibriVox API Discussion thread</a> on our forum.  You are welcome
	to discuss, and to make requests, but please be aware that development depends on limited volunteer time, and we also try to avoid breaking
	changes to the existing API.
</p>

<p>	
<ul>
	<li>Dynamic responses - <strike>I'm going to create a paramter to get a standard simple, standard full set of data</strike> ADDED. Use "extended=1"; simple version is default</li>
	<li>Dynamic field selection - <strike>may get added at the end, but hope to all you to specify the exact set of fields or sub-objects (ie, authors, sections) to include in the response</strike> ADDED.
		See "Requesting specific fields" above.</li>
	<li><strike>Separate endpoint for sections (tracks)</strike> ADDED</li>
	<li><strike>Adding info about time & total time ADDED</strike></li>
	<li><strike>Separate endpoint for authors (just to allow for collecting author info - will be fairly simple, not tied to books I don't think)</strike> ADDED</li>
	<li>Response format in csv, OPML & MARC records - originally planned.  We're not sure that useful MARC records can be made with the information in our database.  Thoughts?</li>
	<li><strike>Add cover-art info to the audiobooks API</strike> ADDED</li>
	<li><strike>Add a per-request maxmimum record limit.  This will be a breaking change for some clients, but it has unfortunately
		<a href="https://librivox.org/2026/09/16/librivox-api-update/">become necessary for performance reasons</a>.</strike> ADDED</li>
	<li>Add rate-limiting, particularly on large requests.  As noted in the post linked above, we may add this if necessary.  If we do, clients making frequent requests may receive HTTP status code 429 "Too Many Requests".</li>
	<li>Add author information for individual secions of "Collection" projects.  Perhaps also make that searchable?</li>
</ul>
</p>

<div style="height:100px;"></div>
