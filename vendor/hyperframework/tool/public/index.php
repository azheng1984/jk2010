#!/usr/bin/env php
<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('HYPERFRAMEWORK_PATH', dirname(ROOT_PATH).DIRECTORY_SEPARATOR);
require HYPERFRAMEWORK_PATH . 'class_loader' . DIRECTORY_SEPARATOR .
    'lib' . DIRECTORY_SEPARATOR . 'ClassLoader2.php';
$classLoader = new Hyperframework\ClassLoader2;
$classLoader->run();
$exceptionHandler = new CommandExceptionHandler;
$exceptionHandler->run();
if (!isset($_SERVER['PWD'])) {
  $_SERVER['PWD'] = getcwd();
}
$app = new CommandApplication;
$app->run();
