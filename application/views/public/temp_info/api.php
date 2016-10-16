<h4>API Info</h4>
===============


<p>The API is currently in: released</p>

<p>DEVELOPERS: have a look at the <a href="#dev_notes">Dev Notes</a> 
	area at the bottom of the page for a more technical explanation of what's going on.</p>

<p>Url to access: <a href="<?= base_url()?>api/feed/audiobooks"><?= base_url()?>api/feed/audiobooks</a></p>

<p>Parameters allowed (current):</p>

<p>
	<ul>
		<li>id - fetches a single record</li>
		<li>since - takes a UNIX timestamp; returns all projects cataloged since that time</li>
		<li>author - all records by that author last name</li>
		<li>title - all matching titles</li>
		<li>genre - all projects of the matching genre</li>
		<li>extended - =1 will return the full set of data about the project</li>
	</ul>

</p>

<p>Note that the title, author & genre may be searched on with ^ as before, to anchor the beginign of the search term.</p>

<p>Example: <a href="<?= base_url()?>api/feed/audiobooks/title/^all"><?= base_url()?>api/feed/audiobooks/title/^all</a></p>

<p>Records default to a limit of 50, offset 0. You may also send these:</p>

<p>
	<ul>
		<li>limit</li>
		<li>offset</li>
	</ul>

</p>

<p>Search may be conducted as:</p>

<p><a href="<?= base_url()?>api/feed/audiobooks/?id=52"><?= base_url()?>api/feed/audiobooks/?id=52</a></p>

<p>or</p>

<p><a href="<?= base_url()?>api/feed/audiobooks/id/52"><?= base_url()?>api/feed/audiobooks/id/52</a></p>

<p>Formats:</p>

<p>Default format is xml, but also currently available are json, jsonp, serialized & php array. Csv, MARC records and OPML coming soon.</p>

<p>Example:</p>


<p><a href="<?= base_url()?>api/feed/audiobooks/?id=52&format=json"><?= base_url()?>api/feed/audiobooks/?id=52&format=json</a></p>


<div style="height:30px;"></div>

<h5>Simple Audiotracks API</h5>

<p>Endpoint: <?= base_url()?>api/feed/audiotracks</p>

<p>Parameters:</p>

<p>
	<ul>
		<li>id - of track itself</li>
		<li>project_id - all tracks for project</li>
	</ul>

</p>

<div style="height:30px;"></div>

<h5>Simple Authors API</h5>

<p>Endpoint: <?= base_url()?>api/feed/authors</p>

<p>Parameters:</p>

<p>
	<ul>
		<li>id - of author</li>
		<li>last_name - exact match</li>
	</ul>

</p>

<div style="height:30px;"></div>

<div id="dev_notes"><h4>Dev Notes</h4></div>

<p>Here is a TODO list of what is currently being worked on. I'll strike these they are added. This is intended to be an open, back and forth communication area, so please feel free to contact us if you have questions, doubts or observations.</p>

<p>	
<ul>
	<li>Dynamic responses - <strike>I'm going to create a paramter to get a standard simple, standard full set of data</strike> ADDED. Use "extended=1"; simple version is default</li>
	<li>Dynamic field selection - <strike>may get added at the end, but hope to all you to specify the exact set of fields or sub-objects (ie, authors, sections) to include in the response</strike> ADDED. See note below for list of fields & usage
	</li>
	<li><strike>Separate endpoint for sections (tracks)</strike> ADDED</li>
	<li><strike>Adding info about time & total time ADDED</strike></li>
	<li><strike>Separate endpoint for authors (just to allow for collecting author info - will be fairly simple, not tied to books I don't think)</strike> ADDED</li>
	<li>Response format in csv, OPML & MARC records</li>
</ul>
</p>

<h5>Fields list:</h5>

<p>You can specify a list of fields to return in the following two ways:</p>

<p>&fields={id,title,authors,url_rss}</p>

<p>&fields[]=id&fields[]=title&fields[]=url</p>

<p>This supports sub-groups (authors, translators, sections, genres) but not fields within the subgroups (yet)</p>

<h5>List of fields:</h5>

<p>
	<ul>
		<li>id</li>
		<li>title</li>
		<li>description</li>
		<li>url_text_source</li>
		<li>language</li>
		<li>copyright_year</li>
		<li>num_sections</li>
		<li>url_rss</li>
		<li>url_zip_file</li>
		<li>url_project</li>
		<li>url_librivox</li>
		<li>url_iarchive</li>
		<li>url_other</li>
		<li>totaltime</li>
		<li>totaltimesecs</li>
		<li>authors</li>
		<li>sections</li>
		<li>genres</li>
		<li>translators</li>

	</ul>
</p>

<div style="height:100px;"></div>