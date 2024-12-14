<?php

class DbTestCase extends CIPHPUnitTestDbTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $CI =& get_instance();
		$CI->load->library('Seeder');
		$CI->seeder->call('DatabaseSeeder');
    }
}
