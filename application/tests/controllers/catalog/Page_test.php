<?php

class Page_test extends TestCase
{
  /**
   * @dataProvider provider
   */
  public function test_index($url, $expected)
  {
    $response = $this->request('GET', array('Page', 'index', $url));
    foreach ($expected as $str) {
      $this->assertStringContainsString($str, $response);
    }
  }

  public static function provider()
  {
    return array(

      // A simple test case with single-quote in title
      array(
        'url' => 'doctors-red-lamp-by-various',
        'expected' => array(
          '<title>The Doctor\'s Red Lamp | LibriVox</title>', // HTML title
          '<a href="https://librivox.org/reader/9835">', // Book Coordinator
          '<a href="https://librivox.org/reader/10179">', // Meta-Coordinator
          '<a href="https://librivox.org/reader/12980">', // Proof-Listener
          '<a href="https://www.gutenberg.org/ebooks/47789">', // Source text
          '<a href="https://librivox.org/author/18">', // Author
          'doctorsredlamp_27_moulton_64kb.mp3', // Last audio (64kb)
          'doctorsredlamp_27_moulton_128kb.mp3', // Last audio (128kb)
        )
      ),

      // Double-quote in title, single in description
      array(
        'url' => 'boy-the-wandering-dog-by-marshall-saunders',
        'expected' => array(
          '<title>"Boy" The Wandering Dog | LibriVox</title>', // HTML title
          '<a href="https://librivox.org/reader/7756">', // Book Coordinator
          '<a href="https://librivox.org/reader/6924">', // Meta-Coordinator
          '<a href="https://librivox.org/reader/5578">', // Proof-Listener
          '<a href="https://www.gutenberg.org/ebooks/50394">', // Source text
          '<a href="https://librivox.org/author/4741"', // Author
          'Another \'dog\'s-eye view\' book for children by', // Description text
          'boythewanderingdog_29_saunders_64kb.mp3', // Last audio (64kb)
          'boythewanderingdog_29_saunders_128kb.mp3', // Last audio (128kb)
        )
      ),

    );
  }
}

