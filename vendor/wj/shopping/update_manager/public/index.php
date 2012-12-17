#!/usr/bin/env php
<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('DATA_PATH', ROOT_PATH.'data'.DIRECTORY_SEPARATOR);
define('IMAGE_PATH', '/home/azheng/Desktop/home/wj_img/');
define('HYPERFRAMEWORK_PATH', '/home/azheng/wj/vendor/hyperframework/');
require HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';
$CLASS_LOADER = new ClassLoader;
$CLASS_LOADER->run();
$EXCEPTION_HANDLER = new CommandExceptionHandler;
$EXCEPTION_HANDLER->run();
$APP = new CommandApplication;
$APP->run();