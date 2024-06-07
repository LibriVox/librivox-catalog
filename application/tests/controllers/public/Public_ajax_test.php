<?php

class Public_ajax_test extends TestCase
{
	public function setUp(): void
	{
		$this->resetInstance();
	}

	/**
	* @dataProvider provider
	*/
	public function test_author_autocomplete($search_terms, $expected, $not_expected) {
        foreach($search_terms as $term_set) {
            $response = $this->request(
                'POST',
                'public/public_ajax/autocomplete_author',
                $term_set,
            );
            foreach ($expected as $str) {
                $this->assertStringContainsString($str, $response);
            }
            foreach ($not_expected as $str) {
                $this->assertStringNotContainsString($str, $response);
            }
        }
	}

	public static function provider() {
		return array(

            // Don't search for empty terms
			array(
                'search_terms' => array(
                    array('search_field' => 'first_name', 'term' => ''),
                    array('search_field' => 'last_name', 'term' => ''),
                    array('search_field' => 'full_name', 'term' => '')
                ),
                'expected' => array("[]"),
                'not_expected' => array("{")
            ),

            // Searches solely by first name (Walter)
			array(
                'search_terms' => array(
                    array('search_field' => 'first_name', 'term' => 'walter')
                ),
                'expected' => array(
                    "http:\/\/en.wikipedia.org\/wiki\/Walter_Flex", // Simple "begin at the beginning" match
                    "http:\/\/en.wikipedia.org\/wiki\/Sam_Walter_Foss", // A "first name" that includes, but does not start with our search term

                    '"id":"12799"', // E. Walter Walters, a first-and-last name match, should not be excluded.

                    "http:\/\/en.wikipedia.org\/wiki\/Walter_Conrad_Arensberg", // A couple more to round things out.
                    "https:\/\/en.wikipedia.org\/wiki\/Walter_R._Brooks"
                ),
                'not_expected' => array(
                    "http:\/\/en.wikipedia.org\/wiki\/Wolfgang_Sartorius_von_Waltershausen", // A "last name"-only match should be excluded
                )
            ),


            // Searches solely by last name (Was[...])
            array(
                'search_terms' => array(
                    array('search_field' => 'last_name', 'term' => 'was')
                ),
                'expected' => array(
                    "https:\/\/en.wikipedia.org\/wiki\/Lemuel_K._Washburn",
                    "https:\/\/en.wikipedia.org\/wiki\/George_Washington",
                    "http:\/\/en.wikipedia.org\/wiki\/Booker_T._Washington",
                    "http:\/\/en.wikipedia.org\/wiki\/Jakob_Wassermann"
                ),
                'not_expected' => array(
                    "https:\/\/en.wikipedia.org\/wiki\/George_Washington_Carver", // A few authors with a 'was' included in the "first" name
                    "https:\/\/prabook.com\/web\/george.smalley\/3768926",
                    "https:\/\/de.wikipedia.org\/wiki\/Stepan_Rudanskyj"
                )
            ),


            // Searches by first name, continuing through to the last name (George Washington[...])
            array(
                'search_terms' => array(
                    array('search_field' => 'first_name', 'term' => 'george washington'),
                    array('search_field' => 'full_name', 'term' => 'george washington')
                ),
                'expected' => array(
                    "https:\/\/en.wikipedia.org\/wiki\/George_Washington", // The man himself

                    "https:\/\/en.wikipedia.org\/wiki\/George_Washington_Bethune", // Various folks with "George Washington" as part of their "first" name
                    "https:\/\/en.wikipedia.org\/wiki\/George_Washington_Cable",
                    "https:\/\/en.wikipedia.org\/wiki\/George_Washington_Carver",
                ),
                'not_expected' => array(
                    "https:\/\/en.wikipedia.org\/wiki\/George_Frederick_Abbott", // A pair of other Georges
                    "http:\/\/en.wikipedia.org\/wiki\/George_William_Bagby",

                    "https:\/\/en.wikipedia.org\/wiki\/Washington_Allston", // Assorted non-George "Washington"-s
                    "http:\/\/en.wikipedia.org\/wiki\/Adolphus_Greely",
                    "Catharine Marguerite Beauchamp"
                )
            ),


            // Searches by both names, either "filtered" or comma-separated ('Jo' + wild + 'ith')
            array(
                'search_terms' => array(
                    array('search_field' => 'first_name', 'term' => 'jo', 'filter_field' => 'last_name', 'filter_term' => 'ith'),
                    array('search_field' => 'last_name', 'term' => 'ith', 'filter_field' => 'first_name', 'filter_term' => 'jo'),
                    array('search_field' => 'last_name', 'term' => 'ith, jo'),
                    array('search_field' => 'full_name', 'term' => 'ith, jo')
                ),
                'expected' => array(
                    "https:\/\/en.wikipedia.org\/wiki\/John_Arrowsmith_(scholar)",
                    "http:\/\/en.wikipedia.org\/wiki\/John_Smith_(explorer)",
                    "https:\/\/de.wikipedia.org\/wiki\/Johann_Philipp_Lorenz_Withof"
                ),
                'not_expected' => array(
                    "https:\/\/en.wikipedia.org\/wiki\/John_Stevens_Cabot_Abbott", // First-name-only match
                    "https:\/\/en.wikipedia.org\/wiki\/H._H._Asquith", // Last-name-only match
                    "Reverend Frederick Robert", // Mixed-match between a pseudonym and a real name: "John Ackworth", AKA "Reverend Frederick Robert Smith"
                )
            ),


            // Searches for authors that have ONLY a last name in the database (Supreme Court)
            array(
                'search_terms' => array(
                    array('search_field' => 'first_name', 'term' => 'supreme court'),
                    array('search_field' => 'last_name', 'term' => 'supreme court'),
                    array('search_field' => 'full_name', 'term' => 'supreme court'),
                    array('search_field' => 'full_name', 'term' => 'court, supreme'),
                    array('search_field' => 'first_name', 'term' => 'supreme', 'filter_field' => 'last_name', 'filter_term' => 'court'),
                    array('search_field' => 'last_name', 'term' => 'court', 'filter_field' => 'first_name', 'filter_term' => 'supreme')
                ),
                'expected' => array(
                    "http:\/\/en.wikipedia.org\/wiki\/Supreme_Court_of_the_United_States"
                ),
                'not_expected' => array(
                    "[]"
                )
            ),


            // Searches by pseudonym (Samuel L. Clemens)
            array(
                'search_terms' => array(
                    array('search_field' => 'first_name', 'term' => 'l. clemens'),
                    array('search_field' => 'full_name', 'term' => 'l. clemens'),
                    array('search_field' => 'last_name', 'term' => 'clemens, sam'),
                    array('search_field' => 'full_name', 'term' => 'clemens, sam'),
                    array('search_field' => 'first_name', 'term' => 'sam', 'filter_field' => 'last_name', 'filter_term' => 'clemens'),
                    array('search_field' => 'last_name', 'term' => 'clemens', 'filter_field' => 'first_name', 'filter_term' => 'sam')
                ),
                'expected' => array(
                    "https:\/\/en.wikipedia.org\/wiki\/Mark_Twain"
                ),
                'not_expected' => array()
            ),
		);
	}
}

?>
