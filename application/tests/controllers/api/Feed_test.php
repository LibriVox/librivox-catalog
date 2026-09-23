<?php

class Feed_test extends TestCase
{
  /**
   * @dataProvider provider
   */
  public function test_audiobooks_get($params, $format, $expected, $not_expected)
  {
		$response = $this->request('GET', '/api/feed/audiobooks/'. $params);
    foreach ($expected as $str) {
      $this->assertStringContainsString($str, $response);
		}

		if (isset($not_expected))
		{
			foreach ($not_expected as $str) {
				$this->assertStringNotContainsString($str, $response);
			}
		}

		// Validate the responses, to make sure they at least parse in their respective formats.
		if ($format == 'xml')
		{
			libxml_use_internal_errors(true);
			$parsedXml = simplexml_load_string($response);
			foreach (libxml_get_errors() as $error) {
				echo "XML parsing error: ", $error->message, "\n";
			}
			$this->assertIsObject($parsedXml); // If there are any errors, $parsedXml will instead be `false`
		}
		elseif ($format == 'json')
		{
			$this->assertJson($response);
		}
		elseif ($format == 'jsonp')
		{
			// TODO: Find a validator for JSONP.  json_decode() and assertJson both have trouble with it.
		}
		elseif ($format == 'serialized')
		{
			$this->assertIsArray(unserialize($response));
		}
		elseif ($format == 'php')
		{
			$this->assertIsArray(eval('return '. $response .';'));
		}
  }

  public static function provider()
  {
    return array(

			// A simple test case by id, formatting it as a path.
			// We'll check that all of the default fields are in, but none of the optional ones.
      array(
				'params' => 'id/19058',
				'format' => 'xml',
				'expected' => array(
					'<id>19058</id>',
					'<title>Doctor\'s Red Lamp</title>',
					'A fictional series of short stories from various authors, each containing stories of doctors.',
					'<url_text_source>https://www.gutenberg.org/ebooks/47789</url_text_source>',
					'<language>English</language>', // Language display name
					'<copyright_year>1904</copyright_year>',
					'<num_sections>28</num_sections>',
					'<url_rss>https://librivox.org/rss/19058</url_rss>',

					'<url_zip_file>', // Note: the URL isn't on the same line, unlike other fields.
					// Also note: the format of Archive links has been updated on live since the DB dump.  The file name at the end should be the same.
					'doctors_red_lamp_2312_librivox.zip',

					'<url_librivox>https://librivox.org/doctors-red-lamp-by-various/</url_librivox>',

					// Skipping '<url_other/>'.  That field is not used in this project, and I'm not sure we care if the
					// empty tag is removed in future.

					'<totaltime>', // Also skipping the actual value here, in case of fixes on live.
					'<totaltimesecs>',
					'<authors>',
					'<author>',
					'<id>18</id>',
					// Skipping <first_name/>, which is blank for this pseudo-author.  Again, empty XML tag.
					'<last_name>Various</last_name>',
					// Also skipping dates of birth and death, for the same reason.
				),
				'not_expected' => array(
					'<id>47</id>', // The lowest-numbered project.  It shouldn't slip in here.
					// Formatting aside from XML
					'"title":',
					'{',
					'=>',
					'array (',
					// Fields aside from the default.  We're not expecting even empty tags of these, unless requested.
					'<url_iarchive',
					'<coverart',
					'<sections',
					'<genres',
				)
      ),

      // Another with ?id= formatting, and a limited set of fields.  Even though we set coverart=1, we only have coverart_thumbnail in our field list.
      array(
				'params' => '?id=10389&fields[]=title&fields[]=url_librivox&fields[]=description&fields[]=authors&coverart=1&fields[]=coverart_thumbnail',
				'format' => 'xml',
        'expected' => array(
          '<title>"Boy" The Wandering Dog</title>', // Project title
          '<url_librivox>https://librivox.org/boy-the-wandering-dog-by-marshall-saunders/</url_librivox>', // Catalog page
					'Another \'dog\'s-eye view\' book for children by', // Description text
					'<id>4741</id>', // Author ID
          '<last_name>Saunders</last_name>'
				),
				'not_expected' => array(
					'10389', // the book/project ID, since we did not allow/request that field.
					'url_text_source',
					'<language>',
					'copyright_year',
					'num_sections',
					'url_rss',
					'url_zip_file',
					'url_project',
					'librivox_iarchive',
					'url_other',
					'totaltime',
					'sections',
					'genres',
					'translators',
					'coverart_jpg',
					'coverart_pdf'
				)
      ),

      // A result of searching by author, with a fairly high limit set (in case of new books!), and with the 'extended' fields present.
      array(
				'params' => '?author=^Leblanc&limit=250&extended=1',
				'format' => 'xml',
				'expected' => array(
					'<id>8604</id>', // Project ID of one result
					'<id>16769</id>', // And another
					'<reader_id>16256</reader_id>', // A reader's ID
					'<title>Extraordinary Adventures of Ars', // Partial title of one result.
          'A contemporary of Arthur Conan Doyle, Maurice Leblanc (1864-1941) was the creator of the character of gentleman thief', //Partial description
          '<url_text_source>https://www.gutenberg.org/etext/6133</url_text_source>',
					'<language>English</language>', // A language that should exist in these results
					'<language>French</language>', // Another
					'<copyright_year>1907</copyright_year>',
					'<num_sections>16</num_sections>',
					'<url_rss>https://librivox.org/rss/2212</url_rss>',
					'/aiguillecreuse_2109_librivox.zip', // Part of a .zip URL.  Again, the rest of the URL may have changed on live since he DB dump.
					'<url_project>', // These are more likely to change than the rest of the fields.  Leaving the check for only the tag.
					'https://librivox.org/les-confidences-darsene-lupin-by-maurice-leblanc/', // a url_librivox entry
					'https://www.archive.org/details/eight_strokes_lr_0811_librivox', // a 'url_iarchive' entry
					'<url_other', // This tag is expected to be present, even if empty
					'<totaltime>',
					'<totaltimesecs>',
					'<authors>',
					'<author>',
					'<id>2350</id>', // The author's ID
					'<first_name>Maurice</first_name>',
					'<last_name>Leblanc</last_name>',
					'<dob>1864</dob>', // Dates (years, rather) of birth and death
					'<dod>1941</dod>',
					'<sections>',
					'<section>',
					'<id>550512</id>', // A section ID
					'<section_number>6</section_number>',
					'<title>Un secret historique</title>',
					'<listen_url>',
					'https://www.archive.org/download/aiguillecreuse_2109_librivox/aiguillecreuse_10_leblanc_', // Leaving out the '64kb part' in case we ever link to the 128kb versions.
					'<playtime>',
					'<genres>',
					'<id>5</id>',
					'<name>', // Genre ID 5 is currently 'Crime & Mystery Fiction', but that display name may be changed.  Check whether tag is present.
					'<translators>',
					'<translator>',
					'<id>6493</id>', // The translator for one of the English-language texts.
					'<dob>1865</dob>', // These dates are unlikely to change.  The exact contents of the name fields may (this one has a leading space, in the dumped DB version)
					'<dod>1921</dod>',
				),
				'not_expected' => array(
					'<coverart_' // We did not request any of the cover-art fields.  If they are present, that's a bug.
				)
      ),

			// A "Collection" or "compilation" project, with all fields enabled.
      array(
				'params' => 'format/xml/extended/1/coverart/1?title=^Multilingual Short Works Collection 032',
				'format' => 'xml',
				'expected' => array(
					'<id>17784</id>', // Project ID
					'Multilingual Short Works Collection 032 - Poetry &amp; Prose', // Project title (it's not all on one line with the tags)
					'<language>Multilingual</language>', // The project overall language ("Multilingual" is the one to choose when individual sections may differ)
					'<copyright_year', // Present, but empty (sometimes 0)
					'<coverart_jpg>',
					'https://archive.org/download/multilingualshortworks_032_2208_librivox/mswc032_2208.jpg',
					'<coverart_pdf>',
					'https://archive.org/download/multilingualshortworks_032_2208_librivox/mswc032_2208.pdf',
					'<coverart_thumbnail>',
					'https://archive.org/download/multilingualshortworks_032_2208_librivox/mswc032_2208_thumb.jpg',
					'<url_iarchive>',
					'https://archive.org/details/multilingualshortworks_032_2208_librivox',
					'<id>569794</id>', // The ID of a relevant section
					'<title>French - Comment Emmelka Pougatchev me donna', // Part of section title
					'<language>French</language', // Section language
					'https://www.archive.org/download/multilingualshortworks_032_2208_librivox/msw032_01_commentemmelkapougatchev_tolstoi_chj_', // Listen URL (minus bitrate at the end, which may change)
					'<playtime>',
					'<file_name>',
					'<reader_id>12709</reader_id>',
					'<language>Yiddish</language>', // A later section is in Yiddish.
					'<genre>',
					'<id>62</id>', // Genre 62 is currently named "Anthologies", but that may change.
				),
				'not_expected' => array(
					'<language>Russian</language>', // A language that's not in this particular Collection
					'<id>47</id>', // The lowest-numbered project ID in the whole database (but not published as of DB dump date)
					'<id>52</id>' // Next one up
				)
			),

			// A basic series of tests to show sort_order, count, and limit are functional.
			array(
				'params' => '?author=Austen&limit=2', // The first two projects by ID number, ascending.
				'format' => 'xml',
				'expected' => array(
					'<id>86</id>', // Lowest-numbered project by this author
					'<id>219</id>' // Next one up
				),
				'not_expected' => array(
					'<id>253</id>' // And, the third.
				)
			),
			array(
				'params' => '?author=Austen&limit=1&offset=1&sort_order=asc', // Just the second project, and we're specifying the same order as default.
				'format' => 'xml',
				'expected' => array(
					'<id>219</id>', // Second-lowest project by this author
				),
				'not_expected' => array(
					'<id>86</id>', // Lowest
					'<id>253</id>' // Third-lowest
				)
			),
			array(
				'params' => 'sort_order/desc/limit/5/author/Austen', // Opposite sort order.
				'format' => 'xml',
				'expected' => array(
					// The highest-numbered project in the dataset.  Leaving blank, as that will change.
				),
				'not_expected' => array(
					'<id>47</id>',
					'<id>52</id>',
					'<id>86</id>'
				)
			),
			array(
				'params' => '', // Defaults to first 50 projects.
				'format' => 'xml',
				'expected' => array(
					'<id>52</id>',
					'<id>132</id>' // 50th project by ID
				),
				'not_expected' => array(
					'<id>133</id>' // 51st
				)
			),
			array(
				'params' => '?limit=100&offset=50&fields=id',
				'format' => 'xml',
				'expected' => array(
					'<id>133</id>', // 51st
					'<id>238</id>' // 150th
				),
				'not_expected' => array(
					'<id>132</id>', // 50th
					'<id>239</id>', // 151st
					'title', // Any fields apart from ID
					'authors',
					'genres'
				)
			),

			// Simple test of 'since' parameter
			// NOTE: This query will have slightly different results depending on the server's time zone, hence the more flexible test.
			// Currently, the live server is on Eastern Daylight Time (US/Los Angeles).
			array(
				'params' => '?since=1643155200&fields=id&limit=10', // 2022-01-26, the "date_catalog" for project ID 2306.  This timestamp is for midnight, UTC.
				'format' => 'xml',
				'expected' => array(
					'<id>708</id>',
					'<id>2306</id>',
					'<id>2496</id>',
					'<id>2652</id>',
				),
				'not_expected' => array(
					'<id>47</id>',
					'<id>52</id>',
					'<id>4246</id>',
				)
			),
			array(
				'params' => '?since=1643241599&fields=id&limit=10', // Add 24 hours (minus 1 second), and our project is now too old, according to 'since'.
				'format' => 'xml',
				'expected' => array(
					'<id>708</id>',
					'<id>2496</id>',
					'<id>2652</id>',
				),
				'not_expected' => array(
					'<id>2306</id>',
				)
			),


			// TODO: Test WHERE fields such as 'author', especially in combination (see issue #268)


			// A full, all-fields search with each other output format (either to validate, or just make sure things exist and do not crash)
			array(
				'params' => '?format=json&extended=1&coverart=1',
				'format' => 'json', // We have a validator for this one handy.
				'expected' => array(
					'id', // Fields should at least be present, no matter the formatting.
					'title',
					'coverart_jpg',
					'translators'
				),
				'not_expected' => array(
					'<title', // No XML formatting tags, if you please.
					'<id',
					'<description',
					'<author',
					'<last_name',
					'<genre'
				)
			),
			array(
				'params' => '?format=jsonp&extended=1&coverart=1',
				'format' => 'jsonp', // Not working with our JSON validator.
				'expected' => array(
					'id', // Fields should at least be present, no matter the formatting.
					'title',
					'coverart_jpg',
					'translators'
				),
				'not_expected' => array(
					'<title', // No XML formatting tags, if you please.
					'<id',
					'<description',
					'<author',
					'<last_name',
					'<genre'
				)
			),
			array(
				'params' => '?format=serialized&extended=1&coverart=1',
				'format' => 'serialized',
				'expected' => array(
					'id', // Fields should at least be present, no matter the formatting.
					'title',
					'coverart_jpg',
					'translators'
				),
				'not_expected' => array(
					'<title', // No XML formatting tags, if you please.
					'<id',
					'<description',
					'<author',
					'<last_name',
					'<genre'
				)
			),
			array(
				'params' => '?format=php&extended=1&coverart=1',
				'format' => 'php',
				'expected' => array(
					'id', // Fields should at least be present, no matter the formatting.
					'title',
					'coverart_jpg',
					'translators'
				),
				'not_expected' => array(
					'<title', // No XML formatting tags, if you please.
					'<id',
					'<description',
					'<author',
					'<last_name',
					'<genre'
				)
			),
		);
  }
}


