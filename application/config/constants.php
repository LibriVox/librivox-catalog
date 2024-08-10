<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

// Define Ajax Request - for uploader
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');


//Permissions groups (in Roles table - groups is for catalog)
define('PERMISSIONS_READERS',					'reader');
define('PERMISSIONS_MEMBERS',					'member');
define('PERMISSIONS_ADMIN',						'admin');
define('PERMISSIONS_BCS',						'bc');
define('PERMISSIONS_MCS',						'mc');
define('PERMISSIONS_UPLOADER', 					'uploader');
define('PERMISSIONS_PLS', 						'pl');

//Project types
define('PROJECT_TYPE_SOLO',					'solo');
define('PROJECT_TYPE_COLLABORATIVE',		'collaborative');
define('PROJECT_TYPE_DRAMATIC',				'dramatic');
define('PROJECT_TYPE_POETRY_WEEKLY',		'poetry_weekly');
define('PROJECT_TYPE_POETRY_FORTNIGHTLY',	'poetry_fortnightly');

//Project status
define('PROJECT_STATUS_OPEN',				'open');
define('PROJECT_STATUS_FULLY_SUBSCRIBED',	'fully_subscribed');
define('PROJECT_STATUS_PROOF_LISTENING',	'proof_listening');
define('PROJECT_STATUS_VALIDATION',			'validation');
define('PROJECT_STATUS_COMPLETE',			'complete');
define('PROJECT_STATUS_ABANDONED',			'abandoned');
define('PROJECT_STATUS_ON_HOLD',			'on_hold');

//Section statuses (not actually in use anywhere, here for reference)
define('SECTION_STATUS_OPEN',				'Open');
define('SECTION_STATUS_ASSIGNED',			'Assigned');
define('SECTION_STATUS_PL_READY',			'Ready for PL');
define('SECTION_STATUS_PL_NOTES',			'See PL notes');
define('SECTION_STATUS_PL_SPOT',			'Ready for spot PL');
define('SECTION_STATUS_PL_OK',				'PL OK');


//Directories
define('DIR_VALIDATOR',					'librivox-validator-books');
define('DIR_READER_UPLOAD',				'uploads');

//Links
define('PEOPLE_LINK', '/reader/'); // 'https://catalog.librivox.org/people_public.php?peopleid='
define('LICENSE_LINK', 'http://creativecommons.org/publicdomain/mark/1.0/');
define('UPLOADER_LINK', 'login/uploader');

//Image paths
define('IMG_PATH_RESULTS_LOGIN',	'img/login.jpg');


//Catalog
define('CATALOG_RESULT_COUNT',				25);

define ('AUTOCOMPLETE_LIMIT', 100);

define ('ALL_EXCEPT_ENGLISH', -1);


/* End of file constants.php */
/* Location: ./application/config/constants.php */
