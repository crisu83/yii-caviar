<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

$vendor = dirname(__DIR__) . '/../..';

require($vendor . '/autoload.php');
require($vendor . '/yiisoft/yii/framework/yii.php');

$basePath = __DIR__ . '/_data';

$config = array(
    'basePath' => $basePath,
    'runtimePath' => $basePath . '/runtime',
    'commandMap' => array(
        'generate' => array(
            'class' => '\crisu83\yii_caviar\GenerateCommand',
            'basePath' => $basePath,
        ),
    ),
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=sakila',
            'username' => 'root',
            'password' => 'appetizer',
            'charset' => 'utf8',
        ),
    ),
);

Yii::createConsoleApplication($config)->run();