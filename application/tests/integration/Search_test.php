<?php

class Search_test extends TestCase
{
	public function setUp(): void
	{
		$this->resetInstance();
	}

	public function test_advanced_search_empty_result() {
		$response = $this->ajaxRequest(
			'GET',
			'/advanced_search',
			[
				'title' => '',
				'author' => '',
				'reader' => '',
				'keywords' => '',
				'genre_id' => '0',
				'status' => 'all',
				'project_type' => 'either',
				'recorded_language' => '',
				'sort_order' => 'alpha',
				'search_page' => '1',
				'search_form' => 'advanced',
				'q' => '233745c3b1aa56daa927ca394282345f', // Unique string
			]
		);

		$this->assertResponseCode(200);

		$body_json = json_decode($response, $associative = true);
		$this->assertEquals('SUCCESS', $body_json['status']);
		$this->assertEquals('No results found', $body_json['results']);
		$this->assertEquals('', $body_json['pagination']);
		$this->assertEquals('1', $body_json['search_page']);
	}

	public function test_advanced_search_at_least_one_result() {
		$unique_string = uniqid();

		$this->CI->load->model('author_model');
		$author_id = $this->CI->author_model->insert([
			'first_name' => 'A',
			'last_name' => 'B',
		]);

		$this->CI->load->model('project_model');
		$project_id = $this->CI->project_model->insert([
			'title_prefix' => '',
			'title' => $unique_string,
			'language_id' => 1,
			'status' => 'complete',
			'project_type' => 'solo'
		]);

		$this->CI->load->model('project_author_model');
		$author_id = $this->CI->project_author_model->insert([
			'project_id' => $project_id,
			'author_id' => $author_id,
			'type' => 'author',
		]);

		// Important! We need to rebuild `search_table` or we won't find
		// any search results
		$this->request(
			'POST',
			'/cron/Search_table_update/search_table',
		);

		$response = $this->ajaxRequest(
			'GET',
			'/advanced_search',
			[
				'title' => '',
				'author' => '',
				'reader' => '',
				'keywords' => '',
				'genre_id' => '0',
				'status' => 'all',
				'project_type' => 'either',
				'recorded_language' => '',
				'sort_order' => 'alpha',
				'search_page' => '1',
				'search_form' => 'advanced',
				'q' => $unique_string, // Unique string
			]
		);

		$this->assertResponseCode(200);

		$body_json = json_decode($response, $associative = true);
		$this->assertEquals('SUCCESS', $body_json['status']);
		$this->assertStringContainsString($unique_string, $body_json['results']);
		$this->assertEquals('', $body_json['pagination']);
		$this->assertEquals('1', $body_json['search_page']);
	}
}
