#!/usr/bin/php
<?php
ini_set('display_errors', 0);
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_PATH', dirname(dirname(ROOT_PATH)).'/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/hf/');
define('HF_CONFIG_PATH', ROOT_PATH.'config/hf/');
require HF_PATH.'class_loader/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$parser = new CommandParser;
$parser->parse();