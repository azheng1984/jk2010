<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/');

//require ROOT_PATH.'lib/ClassLoader.php';
//$classLoader = new ClassLoader;
//$classLoader->run();
//$errorHandler = new ErrorHandler(new Application(new ViewProcessor));
//$errorHandler->run();

ini_set('include_path', '');

$app = new Application(new ActionProcessor, new ViewProcessor);
$app->run();