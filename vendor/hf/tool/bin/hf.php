#!/usr/bin/php
<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_CORE_PATH', dirname(ROOT_PATH).'/core/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/');

require HF_CORE_PATH.'lib/ClassLoader.php';
$classLoader = new ClassLoader;
//$classLoader->run();

$includePath = str_replace('\\', '/', get_include_path().
';'.HF_CORE_PATH.'lib'.
';'.HF_CORE_PATH.'lib/Processor'.
';'.HF_CORE_PATH.'lib/Exception'.
';'.ROOT_PATH.'lib'.
';'.ROOT_PATH.'lib/Processor'.
';'.ROOT_PATH.'app/project');
set_include_path($includePath);
function __autoload($name) {
  require "$name.php";
}

//$router = new Router;
$_SERVER['REQUEST_METHOD'] = 'GET';
$app = new Application(new ActionProcessor, new ViewProcessor('cli'));
$app->run('project');