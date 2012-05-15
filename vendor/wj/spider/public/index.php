#!/usr/bin/env php
<?php

define('ROOT_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
$GLOBALS['no_image_md5'] = array(
  'aff02bb2aaa921e4671522b94f0061a5' => true,
  '074f08d6ad97e753c7d99553c7fa530a' => true,
);
require ROOT_PATH.'public'.DIRECTORY_SEPARATOR.'env.php';
require HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$exceptionHandler = new CommandExceptionHandler;
$exceptionHandler->run();
$app = new CommandApplication;
$app->run();