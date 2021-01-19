<?php  if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * CodeIgniter Monolog integration
 *
 */
/* GENERAL OPTIONS */
// valid handlers are file | new_relic | hipchat
$config['handlers'] = array('file');
$config['channel'] = ENVIRONMENT; // channel name which appears on each line of log
$config['log_threshold'] = 2; // 'ERROR' => 1, 'DEBUG' => 2,  'INFO' => 3, 'ALL' => 4
$config['introspection_processor'] = false; // add some meta data such as controller and line number to log messages
/* FILE HANDLER OPTIONS
 * Log to default CI log directory (must be writable ie. chmod 757).
 * Filename will be encoded to current system date, ie. YYYY-MM-DD-ci.log
*/
$config['file_logfile'] = APPPATH . 'logs/pfi_log';
$config['file_number_rotate'] = 2;
/* NEW RELIC OPTIONS */
$config['new_relic_app_name'] = ' - ' . ENVIRONMENT;

/* SENTRY OPTIONS */
$config['sentry_dsn'] = 'http://e53c11f6c07f426b816830657e600848:8a6e72e7d15b46b4926b7409f7f7f61f@sentry.test.com:9445/2';

/* HIPCHAT OPTIONS */
$config['hipchat_app_token'] = ''; //HipChat API Token
$config['hipchat_app_room_id'] = ''; //The room that should be alerted of the message (Id or Name)
$config['hipchat_app_notification_name'] = 'Monolog'; //Name used in the "from" field
$config['hipchat_app_notify'] = false; //Trigger a notification in clients or not
//$config['hipchat_app_loglevel'] = Logger::WARNING; //The minimum logging level at which this handler will be triggered
// exclusion list for pesky messages which you may wish to temporarily suppress with strpos() match
$config['exclusion_list'] = array();
