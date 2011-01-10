<?php
define('SITE_PATH', dirname(dirname(__FILE__)).'/');
require SITE_PATH.'vendor/hf/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$viewProcessor = new ViewProcessor;
$errorHandler = new ErrorHandler(new Application($viewProcessor));
$errorHandler->run();
$app = new Application(new ActionProcessor, $viewProcessor);
$router = new Router;
$app->run($router->getPath());