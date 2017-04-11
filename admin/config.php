<?php
//HTTP
define('HTTP_SERVER', 'http://121.42.254.23/admin');
define('HTTP_CATALOG', 'http://121.42.254.23/static/');
define('HTTP_IMAGE', HTTP_CATALOG);

//DIR
define('DIR_BASE', dirname(dirname(__FILE__)));
define('DIR_APPLICATION', DIR_BASE . '/admin/');
define('DIR_SYSTEM', DIR_BASE . '/system/');
define('DIR_STATIC', DIR_BASE . '/static/');
define('DIR_TEMPLATE', DIR_BASE . '/admin/view/template/');
define('DIR_LANGUAGE', DIR_BASE . '/admin/language/');
define('DIR_CONFIG', DIR_BASE . '/system/config/');
define('DIR_MODIFICATION', DIR_BASE . '/system/storage/modification/');
define('DIR_CACHE', DIR_BASE . '/system/storage/cache/');
define('DIR_DOWNLOAD', DIR_BASE . '/system/storage/download/');
define('DIR_LOGS', DIR_BASE . '/system/storage/logs/');
define('DIR_UPLOAD', DIR_BASE . '/system/storage/upload/');
define('WX_SSL_CONF_PATH', DIR_SYSTEM . 'library/payment/cert/');

//DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'mbdc');
define('DB_PASSWORD', 'mbdc123mm');
define('DB_PORT', '3306');
define('DB_DATABASE', 'mbdc');
define('DB_PREFIX', 'rich_');
//DB
//define('DB_DRIVER', 'mysqli');
//define('DB_HOSTNAME', 'localhost');
//define('DB_USERNAME', 'root');
//define('DB_PASSWORD', 'admin');
//define('DB_PORT', '3306');
//define('DB_DATABASE', 'roach_bicycle');
//define('DB_PREFIX', 'rich_');

//CACHE
define('CACHE_HOSTNAME', '121.42.254.23');
define('CACHE_PORT', '11211');
define('CACHE_PREFIX', 'roachBike');
define('QUEUE_OPEN', true);

//Redis
define('REDIS_HOST', '121.42.254.23');
define('REDIS_PORT', '6379');

define('TIMESTAMP', time());

define('SMS_TIMEOUT', 60 * 3);//短信失效时间，单位是秒
define('USER_ID', '20170315chicilon');
define('USER_KEY', 'chicilon');
define('MIN_RECHARGE', '1'); //最小的充值金额
define('MAX_RECHARGE', '100');//最大的充值金额
define('GAP_TIME', '120');//回传时间120秒
define('API_URL', 'http://47.90.39.93:8888?version=1');
define('OPEN_VALIDATE', false);


define('OFFLINE_THRESHOLD', 3610); //后台认为单车失联（离线）的判断标准（单位秒）
