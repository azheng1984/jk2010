#!/usr/bin/env php
<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('FTP_PATH', '/home/t/');
require ROOT_PATH.'public'.DIRECTORY_SEPARATOR.'env.php';
require HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$exceptionHandler = new CommandExceptionHandler;
$exceptionHandler->run();
$app = new CommandApplication;
$app->run();