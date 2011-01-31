#!/usr/bin/php
<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_CLI_PATH', dirname(ROOT_PATH).'/');
define('HF_CORE_PATH', ROOT_PATH.'cache/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/');
define('HF_CONFIG_PATH', ROOT_PATH.'config/');
require HF_CLI_PATH.'lib/ClassLoader.php';
//$classLoader = new ClassLoader;
//$classLoader->run();

$includePath = str_replace('\\', '/', get_include_path().
';'.ROOT_PATH.'lib'.
';'.ROOT_PATH.'lib/Processor'.
';'.ROOT_PATH.'app/project');
set_include_path($includePath);
function __autoload($name) {
  require "$name.php";
}

//hf options & args - level 1 command
//hf command options & args - level 2 command
$parser = new CommandParser;
$parser->run();