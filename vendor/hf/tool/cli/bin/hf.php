#!/usr/bin/php
<?php
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))).'/');
define('CLI_TOOL_PATH', ROOT_PATH.'cli/');
define('HF_PATH', dirname(ROOT_PATH).'/');
define('HF_CACHE_PATH', CLI_TOOL_PATH.'cache/');

require HF_PATH.'lib/ClassLoader.php';
$classLoader = new ClassLoader;
//$classLoader->run();

set_include_path(get_include_path().':'.HF_PATH.'lib'.':'.HF_PATH.'lib/Exception'.':'.ROOT_PATH.'lib:'.ROOT_PATH.'cli/lib');
function __autoload($name) {
  require "$name.php";
}

$app = new Application(new CommandProcessor($argc, $argv));
$app->run('new');