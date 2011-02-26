<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/vendor/hf/');
define('HF_CONFIG_PATH', ROOT_PATH.'config/vendor/hf/');
define('DATA_PATH', ROOT_PATH.'data/');
require DATA_PATH.'define.php';
require ROOT_PATH.'vendor/hf/class_loader/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$app = new Application(
  array('action' => new ActionProcessor, 'view' => new ViewProcessor)
);
$errorHandler = new ErrorHandler($app);
$errorHandler->run();
$router = new Router;
$app->run($router->getPath());