#!/usr/bin/php
<?php
define('HF_TOOL_PATH', dirname(dirname(dirname(__FILE__))).'/');
define('ROOT_PATH', ROOT_PATH.'cli/');
define('HF_CORE_PATH', dirname(ROOT_PATH).'/core/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/');

require HF_CORE_PATH.'lib/ClassLoader.php';
$classLoader = new ClassLoader;
//$classLoader->run();

set_include_path(get_include_path().':'.HF_CORE_PATH.'lib'.':'.HF_CORE_PATH.'lib/Exception'.':'.ROOT_PATH.'lib:'.ROOT_PATH.'cli/lib');
function __autoload($name) {
  require "$name.php";
}

//$router = new Router;
$app = new Application(new CommandProcessor($argc, $argv));
$app->run('new');