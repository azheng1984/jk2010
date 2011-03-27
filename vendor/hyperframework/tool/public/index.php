#!/usr/bin/env php
<?php
ini_set('display_errors', 0);
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('HYPERFRAMEWORK_PATH', dirname(ROOT_PATH).DIRECTORY_SEPARATOR);
require(
  HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php'
);
$classLoader = new ClassLoader;
$classLoader->run();
$errorHandler = new CommandErrorHandler;
$errorHandler->run();
$app = new CommandApplication;
$app->run();