<?php
//HTTP
define('HTTP_SERVER', 'http://bike.e-stronger.com/ebike/apien');
define('HTTP_IMAGE', 'http://bike.e-stronger.com/ebike/static/');
define('HTTP_STATIC', 'http://bike.e-stronger.com/ebike/static/');

//DIR
define('DIR_BASE', dirname(dirname(__FILE__)));
define('DIR_APPLICATION', DIR_BASE . '/apien/');
define('DIR_SYSTEM', DIR_BASE . '/system/');
define('DIR_STATIC', DIR_BASE . '/static/');
define('DIR_TEMPLATE', DIR_BASE . '/apien/view/template/');
define('DIR_LANGUAGE', DIR_BASE . '/apien/language/');
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

define('SMS_TIMEOUT', 60 * 5);//短信失效时间，单位是秒
define('USER_ID', '2016121288yiqiang');
define('USER_KEY', 'yiqiang');
define('MIN_RECHARGE', 0.01); //最小的充值金额
define('MAX_RECHARGE', 100);//最大的充值金额
define('GAP_TIME', 120);//回传时间120秒
define('API_URL', 'http://47.90.39.93:8888?version=1');
define('OPEN_VALIDATE', false);
define('DEPOSIT', 99); //押金

define('INIT_STATE', 0); //初始状态
define('INIT_DEPOSIT', 1);//交完押金
define('INIT_IDENTITY', 2);//实名认证完
define('INIT_RECHARGE', 3);//已充值状态
define('CREDIT_POINT', 100);

define('TIME_CHARGE_UNIT', 30 * 60);//计费单位/s
define('PRICE_UNIT', 0.5);//价格单元
define('BOOK_EFFECT_TIME', 15 * 60);
define('RECOMMEND_POINT', 1);

define('MD5_KEY', '42ca79ae07cfc60138edc0f04f2f7eba2e0cdae6');

define('UPDATE_MOBILE_INTERVAL', 3 * 30 * 24 * 60 * 60); //3个月（90天）内只能换一次手机号

define('WX_SSL_CONF_PATH', DIR_SYSTEM . 'library/payment/cert/');

define('VERSION_FAIL', 1);
