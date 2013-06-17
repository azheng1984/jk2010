<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('DATA_PATH', ROOT_PATH.'data'.DIRECTORY_SEPARATOR);
define(
  'HYPERFRAMEWORK_PATH',
  'vendor' . DIRECTORY_SEPARATOR . 'hyperframework' . DIRECTORY_SEPARATOR
);
require DATA_PATH . 'define.php';
require ROOT_PATH . HYPERFRAMEWORK_PATH . 'class_loader' . DIRECTORY_SEPARATOR .
    'lib' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$app = new Application;
$exceptionHandler = new ExceptionHandler($app);
$exceptionHandler->run();
$router = new Router;
$app->run($router->getPath());
