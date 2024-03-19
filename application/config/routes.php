<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/


$route['default_controller'] 	= "public/home/temp_home";
$route['404_override'] = '';

//Public

$route['workflow']				= 'public/home';

$route['home']					= 'public/home';
$route['reader_dashboard']		= 'public/home/dashboard';
$route['add_project'] 			= "public/project_launch";
$route['public_readers_iframe/(:num)'] = "public/public_readers_iframe/index/$1";
$route['public/(:any)']			= 'public/$1';
$route['public/(:any)/(:any)']	= 'public/$1/$2';

//Uploader
$route['uploader']				= 'public/uploader/index';
$route['upload_file']			= 'public/uploader/upload_file';


//Protected
$route['login/(:any)']			= 'auth/login/$1';
$route['login']					= 'auth/login';
$route['logout']				= 'auth/logout';
$route['auth/(:any)']			= 'auth/$1';
$route['auth/(:any)/(:any)']	= 'auth/$1/$2';

//this will redirect away from workflow area
//$route['workflow-help']			= '';


$route['manage_dashboard']		= 'private/administer_projects/index';
$route['add_catalog_item']		= 'private/administer_projects/add_catalog_item';
$route['add_catalog_item/(:any)']	= 'private/administer_projects/add_catalog_item/$1';
$route['section_compiler']		= 'private/section_compiler/index';
$route['section_compiler/(:any)']	= 'private/section_compiler/index/$1';


$route['validator']				= 'private/validator/index';
$route['validator/(:num)']		= 'private/validator/index/$1';
$route['validator/adjust_file_volume/(:num)']		= 'private/validator/adjust_file_volume/$1';

$route['volunteers']			= 'private/volunteers/index';
$route['volunteers/(:any)']		= 'private/volunteers/index/$1';

$route['projects']				= 'private/projects/index';
$route['projects/(:any)']		= 'private/projects/index/$1';

$route['stats']					= 'private/stats/index';
$route['stats/(:num)']			= 'private/stats/index/$1';
$route['stats/mc_stats']		= 'private/stats/mc_stats';
$route['stats/sections']		= 'private/stats/sections';
$route['stats/sections/(:num)']		= 'private/stats/sections/$1';
$route['stats/monthly_stats']	= 'private/stats/monthly_stats';
$route['stats/general_stats']	= 'private/stats/general_stats';
$route['stats/chapters_count']	= 'private/stats/chapters_count';
$route['stats/chapters_count/(:num)']	= 'private/stats/chapters_count/$1';
$route['stats/statistics']		= 'private/stats/statistics';
$route['stats/active_projects']		= 'private/stats/active_projects';



// Catalog
$route['catalog'] 				= "private/catalog/index";
$route['catalog/(:any)'] 		= "private/catalog/index/$1";

$route['search']				= 'catalog/search';
$route['search/get_results']	= 'catalog/search/get_results';
$route['search/(:any)']			= 'catalog/search/index/$1';
$route['search/(:any)/(:any)']	= 'catalog/search/index/$1/$2';
$route['advanced_search']		= 'catalog/search/advanced_search';


//$route['author'] 				= "catalog/author/index";
$route['author/(:num)'] 		= "catalog/author/index/$1";
$route['author/get_results'] 	= "catalog/author/get_results";

$route['reader/(:num)'] 		= "catalog/reader/index/$1";
$route['reader/get_results'] 	= "catalog/reader/get_results";

$route['group/(:num)'] 			= "catalog/group/index/$1";
$route['group/get_results'] 	= "catalog/group/get_results";

$route['keywords/(:num)'] 			= "catalog/keywords/index/$1";
$route['keywords/get_results'] 	= "catalog/keywords/get_results";

$route['sections/readers/(:num)']		= 'catalog/sections/readers/$1';

//RSS
$route['rss/latest_releases']	= 'rss/rss/latest_releases';
$route['rss/(:num)']			= 'rss/rss/index/$1';


// Logged in areas - general
$route['private/(:any)']				= 'private/$1';
$route['private/(:any)/(:any)']			= 'private/$1/$2';
$route['private/(:any)/(:any)/(:any)']	= 'private/$1/$2/$3';

// Logged in areas - Admin
$route['admin/(:any)']				= 'admin/$1';
$route['admin/(:any)/(:any)']			= 'admin/$1/$2';
$route['admin/(:any)/(:any)/(:any)']	= 'admin/$1/$2/$3';

// API
$route['api/info']				= 'public/temp_info/api';
$route['api/feed/(:any)']		= 'api/feed/$1';
$route['api/(:any)']			= 'api/feed/$1';

//Cron jobs
$route['cron/(:any)']			= 'cron/$1';
$route['cron/(:any)/(:any)']	= 'cron/$1/$2';

//migrations
$route['scripts/(:any)']			= 'scripts/$1';
$route['scripts/(:any)/(:any)']		= 'scripts/$1/$2';
$route['scripts/(:any)/(:any)/(:any)']		= 'scripts/$1/$2/$3';

//testing
$route['test_post'] 			= "public/project_launch/test_post";
$route['test_title'] 			= "public/project_launch/test_title";
$route['testing'] 				= "testing";
$route['testing/(:any)'] 		= "testing/$1";

$route['timeout'] 				= "timeout";


$route['page/(:any)'] 			= "catalog/page/index/$1";  //we don't really use this, but boy! it's confusing without it
$route['(:any)']				= "catalog/page/index/$1";

/* End of file routes.php */
/* Location: ./application/config/routes.php */
