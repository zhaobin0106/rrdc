<?php
// Site
$_['site_base']         = substr(HTTP_SERVER, 7);
$_['site_ssl']          = false;

// Database
$_['db_autostart']      = true;
$_['db_type']           = DB_DRIVER; // mpdo, mssql, mysql, mysqli or postgre
$_['db_hostname']       = DB_HOSTNAME;
$_['db_username']       = DB_USERNAME;
$_['db_password']       = DB_PASSWORD;
$_['db_database']       = DB_DATABASE;
$_['db_port']           = DB_PORT;

// Actions
$_['action_pre_action']  = array(
	'startup/startup',
	'startup/event',
	'startup/login',
	'startup/base',
	'startup/error',
	'startup/permission',
);

// Actions
//$_['action_default']     = 'dashboard/dashboard';
$_['action_default'] = 'admin/index';
// Action Events
$_['action_event'] = array(
//	'model/*/before' => 'event/debug/before',
//	'controller/*/before' => 'event/debug/before'
);