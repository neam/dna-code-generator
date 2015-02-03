<?php

require(__DIR__ . '/../vendor/autoload.php');

require(__DIR__ . '/../config/env.php');
defined('YII_DEBUG') or define('YII_DEBUG', (boolean)getenv('YII_DEBUG'));
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV'));

require(__DIR__ . '/../vendor/autoload.php');

// Define project directory
$rootPath = __DIR__;

// Include yii 1 and yii 2
require("$rootPath/vendor/slavcodev/yii2-yii-bridge/include.php");

// Include yii 1 app config
$v1AppConfig = require("$rootPath/yii1-config/main.php");

// Create old web application, but NOT run it!
Yii::createWebApplication($v1AppConfig);

// Include yii 2 app config
require(__DIR__ . '/../config/bootstrap.php');

$config = require(__DIR__ . '/../config/main.php');
$application = new yii\web\Application($config);
$application->run();
