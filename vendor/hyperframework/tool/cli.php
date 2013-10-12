#!/usr/bin/env php
<?php
throw new Exception;
namespace Hyperframwork\Tool;

define('Hyperframework\Tool\ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('HYPERFRAMEWORK_PATH', dirname(ROOT_PATH).DIRECTORY_SEPARATOR . 'lib'. DIRECTORY_SEPARATOR);
require HYPERFRAMEWORK_PATH . 'Hyperframework' .
    DIRECTORY_SEPARATOR . 'ClassLoader2.php';
$classLoader = new Hyperframework\ClassLoader2;
$classLoader->run();
$exceptionHandler = new CommandExceptionHandler;
$exceptionHandler->run();
if (!isset($_SERVER['PWD'])) {
    $_SERVER['PWD'] = getcwd();
}
$app = new CommandApplication;
$app->run();
