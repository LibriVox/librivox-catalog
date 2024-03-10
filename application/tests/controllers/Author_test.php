<?php

class Author_test extends TestCase
{
	public function test_zeroth_author_returns_404() {
		$this->request('GET', 'author/0');
		$this->assertResponseCode(404);
	}

	public function test_search_without_author_id_returns_400() {
		$this->request(
			'GET',
			'author/get_results',
			[
				'primary_key' => 0,
				'search_page' => 1,
				'search_order' => 'alpha',
				'project_type' => 'either',
			]
		);
		$this->assertResponseCode(400);
	}
}

?>
