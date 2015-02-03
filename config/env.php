<?php

use \Dotenv;

// Make app config available as PHP constants
define('DNA_PROJECT_PATH', __DIR__.'/..'.'/..'.'/..');
$root = DNA_PROJECT_PATH;
require("$root/vendor/neam/php-app-config/include.php");

$_ENV['SITENAME'] = SITENAME;
$_ENV['SUPPORT_EMAIL'] = SUPPORT_EMAIL;
$_ENV['YII_CODE_GENERATION_ADMIN_PASSWORD'] = YII_CODE_GENERATION_ADMIN_PASSWORD;

Dotenv::load(__DIR__.'/..');

Dotenv::required('YII_DEBUG',["","0","1","true",true]);
Dotenv::required('YII_ENV',['dev','prod','test']);
#Dotenv::required(['YII_TRACE_LEVEL']);
Dotenv::required(['APP_NAME','APP_SUPPORT_EMAIL','APP_ADMIN_EMAIL']);
Dotenv::required(['DATABASE_DSN','DATABASE_USER','DATABASE_PASSWORD']);
