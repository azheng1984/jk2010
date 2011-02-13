<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
require ROOT_PATH.'config/app.config.php';
require ROOT_PATH.'vendor/hf/class_loader/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$errorHandler = new ErrorHandler(new Application(new ViewProcessor));
//$errorHandler->run();
$app = new Application(new ActionProcessor, new ViewProcessor);
$router = new Router;
$app->run($router->getPath());