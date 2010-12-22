<?php
define('SITE_ROOT_DIR', dirname(dirname(__FILE__)).'/');
require SITE_ROOT_DIR.'vendor/hf/lib/ClassLoader.php';
ClassLoader::run();
ClassLoader::import('hf');

$app = Router::run();
Action::run($app);
View::run($app);
