<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/vendor/hf/');
define('HF_CONFIG_PATH', ROOT_PATH.'config/vendor/hf/');
define('DATA_PATH', ROOT_PATH.'data/');
require ROOT_PATH.'data/define.php';
require ROOT_PATH.'vendor/hf/class_loader/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$errorHandler = new ErrorHandler(new Application(new ViewProcessor));
//$errorHandler->run();
$app = new Application(new ActionProcessor, new ViewProcessor);
$router = new Router;
$app->run($router->getPath());