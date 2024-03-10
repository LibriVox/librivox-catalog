<?php

class Description_html_render_helper_test extends TestCase
{
	public function setUp(): void
	{
		$this->resetInstance();
		$this->CI->load->helper('description_html_render_helper');
	}

	/**
	* @dataProvider provider
	*/
	public function test_newline_smushing_works($input, $expected) {
		$actual = _normalize_and_deduplicate_newlines_in_html($input);
		$this->assertEquals($expected, $actual);
	}

	public static function provider() {
		return array(
			// Simple case
			array("a b", "a b"),

			// Multiple newlines
			array("a\nb",     "a<br />b"),
			array("a\n\nb",   "a<br /><br />b"),
			array("a\n\n\nb", "a<br /><br />b"),

			// Multiple carriage returns + newlines
			array("a\r\nb",         "a<br />b"),
			array("a\r\n\r\nb",     "a<br /><br />b"),
			array("a\r\n\r\n\r\nb", "a<br /><br />b"),

			// Multiple <br /> tags
			array("a<br />b",             "a<br />b"),
			array("a<br /><br />b",       "a<br /><br />b"),
			array("a<br /><br /><br />b", "a<br /><br />b"),

			// Multiple <br> tags
			array("a<br>b",             "a<br />b"),
			array("a<br><br>b",         "a<br /><br />b"),
			array("a<br><br><br>b",     "a<br /><br />b"),
			array("a<br><br><br><br>b", "a<br /><br />b"),

			// Mixed "newline" things
			array("a<br>\n<br />b",       "a<br /><br />b"),
			array("a<br>\r\n<br />b",     "a<br /><br />b"),
			array("a<br>\n\r\n<br />b",   "a<br /><br />b"),
			array("a\n<br />\r\n<br />b", "a<br /><br />b"),
			array("a\n\r\n<br />b",       "a<br /><br />b"),

			// Case sensitivity, and deduplicating newlines following tags
			array("a<BR>\r\n<BR />\r\nb", "a<br /><br />b"),
			array("a<bR>\n<Br/>\rb",      "a<br /><br />b"),
		);
	}
}

?>
