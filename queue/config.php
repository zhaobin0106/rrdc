<?php
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