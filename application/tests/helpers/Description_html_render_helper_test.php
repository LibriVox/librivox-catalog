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

			/** A bunch of cases omitted for brevity **/

			// Mixed "newline" things
			array("a<br>\n<br />b",       "a<br /><br />b"),
			array("a<br>\r\n<br />b",     "a<br /><br />b"),
			array("a<br>\n\r\n<br />b",   "a<br /><br />b"),
			array("a\n<br />\r\n<br />b", "a<br /><br />b"),
			array("a\n\r\n<br />b",       "a<br /><br />b"),
		);
	}
}

?>
