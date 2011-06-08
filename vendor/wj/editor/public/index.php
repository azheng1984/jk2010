<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('HYPERFRAMEWORK_PATH', 'C:/Documents and Settings/wz/www/jk2010/vendor/hyperframework/');
require HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$app = new Application;
$exceptionHandler = new ExceptionHandler($app);
$exceptionHandler->run();
$app->run();