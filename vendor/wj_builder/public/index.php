#!/usr/bin/env php
<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('HYPERFRAMEWORK_PATH', '/home/wz/www/jk2010/vendor/hyperframework/');
define('WEB_IMAGE_PATH', '/home/wz/wj_web/image/');
define ('IMAGE_PATH', '/home/wz/spider/image/jingdong/');
require HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$exceptionHandler = new CommandExceptionHandler;
$exceptionHandler->run();
$app = new CommandApplication;
$app->run();