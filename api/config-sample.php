<?php
//HTTP
define('HTTP', 'http://api.ebicycle.estronger.cn');

//DIR
define('DIR_BASE', dirname(dirname(__FILE__)));
define('DIR_APPLICATION', DIR_BASE . '/api/');
define('DIR_SYSTEM', DIR_BASE . '/system/');
define('DIR_TEMPLATE', DIR_BASE . '/api/view/template/');
define('DIR_LANGUAGE', DIR_BASE . '/api/language/');
define('DIR_CONFIG', DIR_BASE . '/system/config/');
define('DIR_MODIFICATION', DIR_BASE . '/system/storage/modification/');
define('DIR_CACHE', DIR_BASE . '/system/storage/cache/');
define('DIR_DOWNLOAD', DIR_BASE . '/system/storage/download/');
define('DIR_LOGS', DIR_BASE . '/system/storage/logs/');
define('DIR_UPLOAD', DIR_BASE . '/system/storage/upload/');

//DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_PORT', '3306');
define('DB_DATABASE', 'roach_bike');
define('DB_PREFIX', 'rich_');

//CACHE
define('CACHE_HOSTNAME', 'localhost');
define('CACHE_PORT', '11211');
define('CACHE_PREFIX', 'roachBike'); 