<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL^E_NOTICE);

defined('YII_DEBUG') or define('YII_DEBUG', true);

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

$vendor = realpath(dirname(__DIR__) . '/..');

require("$vendor/autoload.php");
require("$vendor/yiisoft/yii/framework/yii.php");

$basePath = realpath($vendor . '/..');

$config = array(
    'basePath' => $basePath,
    'runtimePath' => '/tmp',
    'commandMap' => array(
        'generate' => array(
            'class' => '\crisu83\yii_caviar\commands\GenerateCommand',
            'basePath' => $basePath,
        ),
    ),
);

Yii::createConsoleApplication($config)->run();