<?php

class API_test extends DbTestCase
{

	public function test_coverart() {
		$output = $this->request(
			'GET',
			'/api/feed/audiobooks/id/52',
			[
				'coverart' => 1,
				'format' => 'json',
			]
		);
        $books = json_decode($output);
        $this->assertEquals(
            $books[0]['coverart_jpg'],
            'https://www.archive.org/download/LibrivoxCdCoverArt12/Letters_Two_Brides_1110.jpg'
        );
        $this->assertEquals(
            $books[0]['coverart_pdf'],
            'https://www.archive.org/download/LibrivoxCdCoverArt12/Letters_Two_Brides_1110.pdf'
        );
        $this->assertEquals(
            $books[0]['coverart_thumbnail'],
            'https://www.archive.org/download/LibrivoxCdCoverArt12/Letters_Two_Brides_1110_thumb.jpg'
        );
	}
}

?>
