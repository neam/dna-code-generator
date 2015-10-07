<?php

define('DNA_PROJECT_PATH', __DIR__.'/..'.'/..'.'/..');
$root = DNA_PROJECT_PATH;

// Include additional DNA classes via composer auto-loading
require_once("$root/vendor/autoload.php");
require_once("$root/dna/vendor/autoload.php");

// root-level bootstrap logic
require("$root/bootstrap.php");

$_ENV['SITENAME'] = SITENAME;
$_ENV['SUPPORT_EMAIL'] = SUPPORT_EMAIL;
$_ENV['YII_CODE_GENERATION_ADMIN_PASSWORD'] = YII_CODE_GENERATION_ADMIN_PASSWORD;

Dotenv::setEnvironmentVariable('DATABASE_DSN', 'mysql:host=' . DATABASE_HOST . ';port=' . DATABASE_PORT . ';dbname=' . DATABASE_NAME);
Dotenv::setEnvironmentVariable('DATABASE_USER', DATABASE_USER);
Dotenv::setEnvironmentVariable('DATABASE_PASSWORD', DATABASE_PASSWORD);

Dotenv::load(__DIR__.'/..');

#Dotenv::required('YII_DEBUG',["","0","1","true",true]);
Dotenv::required('YII_ENV',['dev','prod','test']);
#Dotenv::required(['YII_TRACE_LEVEL']);
Dotenv::required(['APP_NAME','APP_SUPPORT_EMAIL','APP_ADMIN_EMAIL']);
Dotenv::required(['DATABASE_DSN','DATABASE_USER','DATABASE_PASSWORD']);

Dotenv::setEnvironmentVariable('APP_VERSION', file_get_contents(__DIR__.'/../version'));
