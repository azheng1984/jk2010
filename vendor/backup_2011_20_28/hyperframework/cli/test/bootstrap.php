<?php
define('TEST_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('ROOT_PATH', TEST_PATH.'fixture'.DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH.'cache'.DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
define('HYPERFRAMEWORK_PATH', dirname(dirname(TEST_PATH)).DIRECTORY_SEPARATOR);
require HYPERFRAMEWORK_PATH.'class_loader'.DIRECTORY_SEPARATOR
  .'lib'.DIRECTORY_SEPARATOR.'ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();