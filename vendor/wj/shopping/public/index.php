<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('HYPERFRAMEWORK_PATH', '/home/azheng/wj/vendor/hyperframework/');
require HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';
$CLASS_LOADER = new ClassLoader;
$CLASS_LOADER->run();
$APP = new Application;
$EXCEPTION_HANDLER = new ExceptionHandler($APP);
$EXCEPTION_HANDLER->run();
$ROUTER = new Router;
$APP->run($ROUTER->execute()); 