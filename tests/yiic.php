<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL^E_NOTICE);

defined('YII_DEBUG') or define('YII_DEBUG', true);

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

$root = dirname(__DIR__);
$tests = __DIR__;
$vendor = "$root/vendor";

require("$vendor/autoload.php");
require("$vendor/yiisoft/yii/framework/yii.php");

$basePath = "$tests/_data";

$config = array(
    'basePath' => $basePath,
    'runtimePath' => "$basePath/runtime",
    'commandMap' => array(
        'generate' => array(
            'class' => '\crisu83\yii_caviar\commands\GenerateCommand',
            'basePath' => $basePath,
        ),
    ),
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=sakila',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ),
    ),
);

Yii::createConsoleApplication($config)->run();