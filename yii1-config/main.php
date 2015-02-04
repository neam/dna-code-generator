<?php

// convenience variables
$applicationDirectory = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
$projectRoot = $applicationDirectory . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
$baseUrl = (dirname($_SERVER['SCRIPT_NAME']) == '/' || dirname($_SERVER['SCRIPT_NAME']) == '\\') ? '' :
    dirname($_SERVER['SCRIPT_NAME']);

// main application configuration
$config = array(
    'name' => 'DNA Code Generator Yii 1 Web Application',
    'language' => 'en',
    'sourceLanguage' => 'en', // source code language
    'basePath' => $applicationDirectory,
    'aliases' => array(
        'root' => $projectRoot,
        'app' => $applicationDirectory,
        'vendor' => $applicationDirectory . '/../vendor',
        'dna' => $projectRoot . '/dna',
        'app' => 'application',
    ),
    // autoloading model and component classes
    'import' => array(
        'application.components.*',
        'application.controllers.*',
    ),
    // application components
    'components' => array(
        'request' => array(
            'baseUrl' => $baseUrl,
        ),
        'cache' => array(
            'class' => 'CDummyCache',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(),
);

// Import the DNA classes and configuration
require($projectRoot . '/dna/dna-api-revisions/' . YII_DNA_REVISION . '/include.php');

// Extensions' includes
//include($applicationDirectory . '/vendor/neam/yii-dna-debug-modes-and-error-handling/config/error-handling.php');
//include($applicationDirectory . '/vendor/neam/yii-dna-debug-modes-and-error-handling/config/debug-modes.php');

/*
$config['components']['errorHandler'] = array(
    'class' => 'YiiDnaErrorHandler',
);
*/

// Uncomment to easily see the active merged configuration
//echo "<pre>";print_r($config);echo "</pre>";die();

return $config;
