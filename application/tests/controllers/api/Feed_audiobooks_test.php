<?php

/**
 * Regression test for part of issue #268:
 * https://github.com/LibriVox/librivox-catalog/issues/268
 *
 * When both title and author params are provided in _build_data_set(), the
 * subqueries (author, genre) must run before any main query WHERE clauses are
 * added to the shared query builder, otherwise clauses like 'p.title' leak
 * into subqueries that don't have a 'p' table alias, producing invalid SQL.
 */
class Feed_audiobooks_test extends TestCase {

    /**
    * @link https://github.com/LibriVox/librivox-catalog/issues/268
    */
    public function test_title_and_author_combined_does_not_error() {
        $output = $this->request(
            'GET',
            'api/feed/audiobooks?format=json&title=test&author=NONEXISTENT'
        );
        $this->assertResponseCode(200);
    }
}
