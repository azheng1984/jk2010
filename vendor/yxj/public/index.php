<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('DATA_PATH', ROOT_PATH.'data'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
require CONFIG_PATH.'env.config.php';
require HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';
$CLASS_LOADER = new ClassLoader;
$CLASS_LOADER->run();
$APP = new Application;
$EXCEPTION_HANDLER = new ExceptionHandler($APP);
$EXCEPTION_HANDLER->run();
$path = Router::execute();
if ($path !== null) {
  $APP->run($path);
}