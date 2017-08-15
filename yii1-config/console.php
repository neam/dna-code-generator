<?php

$applicationDirectory = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
$projectRoot = $applicationDirectory . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';

$config = array(
    'name' => 'DNA Code Generator Yii 1 Console Application',
    'basePath' => $applicationDirectory,
    'aliases' => array(
        'root' => $projectRoot,
        'app' => $applicationDirectory,
        'vendor' => $applicationDirectory . '/../vendor',
        'dna' => $projectRoot . '/dna',
    ),
    'import' => array(
        'application.commands.components.*',
        'application.behaviors.*',
        'application.components.*',
        'application.controllers.*',
        'application.interfaces.*',
        'application.models.*',
    ),
    'commandMap' => array(),
    'components' => array(
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
);

// Import the DNA classes and configuration into $config
require($projectRoot . '/dna/config/DnaConfig.php');
DnaConfig::applyConfig($config);

// Unset unused configs that come from the dna config.
unset($config['theme']);

// merge configurations
return $config;
