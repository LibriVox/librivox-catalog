<?php

class Project_launch_test extends TestCase
{
	public function test_long_author_names_show_validation_errors() {
		$response = $this->request(
			'POST',
			'add_project',
			[
				'lang_select' => 'english',
				'title' => 'aoeu',
				'is_compilation' => '0',
				'link_to_text' => 'aoeu',
				'project_type' => 'solo',
				'expected_completion_year' => '0',
				'expected_completion_month' => '0',
				'expected_completion_day' => '0',
				'recorded_language' => '1',
				'recorded_language_other' => '',
				'auth_id' => '0',
				'auth_last_name' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
				'auth_first_name' => '',
				'auth_yob' => '',
				'auth_yod' => '',
				'link_to_auth' => '',
				'trans_id' => '0',
				'trans_last_name' => '',
				'trans_first_name' => '',
				'trans_yob' => '',
				'trans_yod' => '',
				'link_to_trans' => '',
				'edition_year' => '',
				'brief_summary' => '',
				'brief_summary_by' => '',
				'link_to_book' => '',
				'pub_year' => '',
				'genres' => '',
				'list_keywords' => '',
				'proof_level' => 'standard',
				'has_preface' => '0',
				'num_sections' => '1',
				'forum_name' => '',
				'soloist_name' => '',
				'soloist_link' => '',
			],
		);
		$this->assertResponseCode(200);
		$this->assertStringContainsString('Last name field can not exceed 255 characters in length', $response);
	}

	public function test_long_author_links_show_validation_errors() {
		$response = $this->request(
			'POST',
			'add_project',
			[
				'lang_select' => 'english',
				'title' => 'aoeu',
				'is_compilation' => '0',
				'link_to_text' => 'aoeu',
				'project_type' => 'solo',
				'expected_completion_year' => '0',
				'expected_completion_month' => '0',
				'expected_completion_day' => '0',
				'recorded_language' => '1',
				'recorded_language_other' => '',
				'auth_id' => '0',
				'auth_last_name' => 'aaaaa',
				'auth_first_name' => '',
				'auth_yob' => '',
				'auth_yod' => '',
				'link_to_auth' => 'https://en.wikipedia.org/wiki/Jos%C3%A9_Joaqu%C3%ADn_Fern%C3%A1ndez_de_Lizardiaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
				'trans_id' => '0',
				'trans_last_name' => '',
				'trans_first_name' => '',
				'trans_yob' => '',
				'trans_yod' => '',
				'link_to_trans' => '',
				'edition_year' => '',
				'brief_summary' => '',
				'brief_summary_by' => '',
				'link_to_book' => '',
				'pub_year' => '',
				'genres' => '',
				'list_keywords' => '',
				'proof_level' => 'standard',
				'has_preface' => '0',
				'num_sections' => '1',
				'forum_name' => '',
				'soloist_name' => '',
				'soloist_link' => '',
			],
		);
		$this->assertResponseCode(200);
		$this->assertStringContainsString('Link to author on Wikipedia field can not exceed 255 characters in length', $response);
	}
}

?>
