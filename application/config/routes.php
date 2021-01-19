<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = false;
$route['home/:num'] = 'home';
$route['wireless/(:num)'] = 'wireless';
$route['network/(:num)'] = 'network';
$route['speedtest/(:num)'] = 'speedtest';
$route['job/(:num)'] = 'job';
$route['job/getjobdetails'] = 'job/getjobdetails';
$route['notifications/test'] = 'notifications/test';
$route['notifications/(:any)/(:any)/(:any)'] = 'notifications';
