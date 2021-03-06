<?php
//HTTP
define('HTTP_SERVER', 'http://121.42.254.23/crontab');
define('HTTP_CATALOG', 'http://121.42.254.23/static/');
define('HTTP_IMAGE', HTTP_CATALOG);

//DIR
define('DIR_BASE', dirname(dirname(__FILE__)));
define('DIR_APPLICATION', DIR_BASE . '/crontab/');
define('DIR_SYSTEM', DIR_BASE . '/system/');
define('DIR_STATIC', DIR_BASE . '/static/');
define('DIR_TEMPLATE', DIR_BASE . '/crontab/view/template/');
define('DIR_LANGUAGE', DIR_BASE . '/crontab/language/');
define('DIR_CONFIG', DIR_BASE . '/system/config/');
define('DIR_MODIFICATION', DIR_BASE . '/system/storage/modification/');
define('DIR_CACHE', DIR_BASE . '/system/storage/cache/');
define('DIR_DOWNLOAD', DIR_BASE . '/system/storage/download/');
define('DIR_LOGS', DIR_BASE . '/system/storage/logs/');
define('DIR_UPLOAD', DIR_BASE . '/system/storage/upload/');

//DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', '121.42.254.23');
define('DB_USERNAME', 'mbdc');
define('DB_PASSWORD', 'mbdc123mm');
define('DB_PORT', '3306');
define('DB_DATABASE', 'mbdc');
define('DB_PREFIX', 'rich_');

//cache
define('CACHE_HOSTNAME', 'localhost');
define('CACHE_PORT', '11211');
define('CACHE_PREFIX', 'roachBike');
define('QUEUE_OPEN', true);

//Redis
define('REDIS_HOST', 'localhost');
define('REDIS_PORT', '6379');
define('TIMESTAMP', time());

define('BOOK_EFFECT_TIME', 15 * 60);