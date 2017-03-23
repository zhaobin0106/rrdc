<?php
//HTTP
define('HTTP_SERVER', 'http://transfer.ebicycle.estronger.cn');
define('HTTPS_SERVER', 'https://transfer.ebicycle.estronger.cn');

//DIR
define('DIR_BASE', dirname(dirname(__FILE__)));
define('DIR_APPLICATION', DIR_BASE . '/transfer/');
define('DIR_SYSTEM', DIR_BASE . '/system/');
define('DIR_TEMPLATE', DIR_BASE . '/transfer/view/template/');
define('DIR_LANGUAGE', DIR_BASE . '/transfer/language/');
define('DIR_CONFIG', DIR_BASE . '/system/config/');
define('DIR_MODIFICATION', DIR_BASE . '/system/storage/modification/');
define('DIR_CACHE', DIR_BASE . '/system/storage/cache/');
define('DIR_DOWNLOAD', DIR_BASE . '/system/storage/download/');
define('DIR_LOGS', DIR_BASE . '/system/storage/logs/');
define('DIR_UPLOAD', DIR_BASE . '/system/storage/upload/');

//DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', '<USERNAME>');
define('DB_PASSWORD', '<PASSWORD>');
define('DB_PORT', '3306');
define('DB_DATABASE', '<DB_NAME>');
define('DB_PREFIX', 'rich_');

//CACHE
define('CACHE_HOSTNAME', 'localhost');
define('CACHE_PORT', '11211');
define('CACHE_PREFIX', 'roachBike');
define('QUEUE_OPEN', true);

//Redis
define('REDIS_HOST', 'localhost');
define('REDIS_PORT', '6379');
define('TIMESTAMP', time());

define('USER_ID', '2016121288yiqiang');
define('USER_KEY', 'yiqiang');
define('MIN_RECHARGE', 10); //最小的充值金额
define('MAX_RECHARGE', '100');//最大的充值金额
define('GAP_TIME', '120');//回传时间120秒
define('API_URL', 'http://47.90.39.93:8888?version=1');
define('OPEN_VALIDATE', false);

//define('PRICE_UNIT', 0.5); //价格单元
//define('TIME_CHARGE_UNIT', 30 * 60);//计费单位
define('BOOK_EFFECT_TIME', 15 * 60);