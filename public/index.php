<?php
define('SITE_PATH', dirname(dirname(__FILE__)).'/');
require SITE_PATH.'vendor/hf/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$view = new View;
$errorHandler = new ErrorHandler(new Application($view));
$errorHandler->run();
$app = new Application(new Action, $view);
$router = new Router;
$app->run($router->getPath());