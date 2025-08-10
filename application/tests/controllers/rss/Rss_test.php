<?php

class Rss_test extends TestCase
{
  /**
   * @dataProvider provider
   */
  public function test_index($id, $expected)
  {
    $response = $this->request('GET', array('rss', 'index', $id));
    foreach ($expected as $str) {
      $this->assertStringContainsString($str, $response);
    }

    libxml_use_internal_errors(true);
    $parsedXml = simplexml_load_string($response);
    foreach (libxml_get_errors() as $error) {
      echo "XML parsing error: ", $error->message, "\n";
    }
    $this->assertIsObject($parsedXml); // If there are any errors, $parsedXml will instead be `false`
  }

  public static function provider()
  {
    return array(

      // A simple test case with single-quote and a prefix ("The") in title
      array(
        'id' => '19058',
        'expected' => array(
          '<title><![CDATA[Doctor\'s Red Lamp, The', // Project title
          '<link><![CDATA[https://librivox.org/doctors-red-lamp-by-various/]]></link>', // Project URL
          '<language><![CDATA[en]]></language>', // Language two-letter code
          'A fictional series of short stories from various authors, each containing stories of doctors.', // Description text
          'doctorsredlamp_27_moulton_64kb.mp3', // Last audio (64kb)
        )
      ),

      // Double-quote in title, single in description
      array(
        'id' => '10389',
        'expected' => array(
          '<title><![CDATA["Boy" The Wandering Dog', // Project title
          '<link><![CDATA[https://librivox.org/boy-the-wandering-dog-by-marshall-saunders/]]></link>', // Project URL
          'Another \'dog\'s-eye view\' book for children by', // Description text
          'boythewanderingdog_29_saunders_64kb.mp3', // Last audio (64kb)
        )
      ),

      // Language other than English with a two-letter code
      array(
        'id' => '2212',
        'expected' => array(
          '<title><![CDATA[Arsène Lupin, gentleman-cambrioleur', // Project title
          '<link><![CDATA[https://librivox.org/arsene-lupin-gentleman-cambrioleur-by-maurice-leblanc/]]></link>', // Project URL
          '<language><![CDATA[fr]]></language>', // Language two-letter code
          'Arsène Lupin, gentleman-cambrioleur est un recueil de nouvelles écrites par Maurice Leblanc et contant les aventures d\'Arsène Lupin.', // Description text
          'lupingentcambrioleur_9_leblanc_64kb.mp3', // Last audio (64kb)
        )
      ),

      // A project with only a three-letter language code (in this case: "Multilingual" to us, or more officially, "Multiple Languages", with code "mul")
      //  Reference: https://www.loc.gov/standards/iso639-2/php/code_list.php
      array(
        'id' => '2462',
        'expected' => array(
          '<language><![CDATA[mul]]></language>', // Language three-letter code
          '<link><![CDATA[https://librivox.org/puisi-dari-indonesia/]]></link>', //Project URL
          'https://www.archive.org/download/puisi_indonesia_0809_librivox/puisidariindonesia_15_sulukwujil_64kb.mp3', // Last audio
        )
      ),

      // TODO: A complete project from a language with no two- or three-letter codes
      //  Currently, "Old Sudanese" has been used only within multilingual collections.  It is not in the reference list, at least under that name.
    );
  }
}


