<?php
define('ROOT_DIR', dirname(dirname(__FILE__)).'/');
require ROOT_DIR.'vendor/hf/lib/ClassLoader.php';
ClassLoader::import('hf');

$app = Router::run();
Action::run($app);
View::run($app);
