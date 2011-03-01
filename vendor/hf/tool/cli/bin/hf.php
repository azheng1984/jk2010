#!/usr/bin/php
<?php
ini_set('display_errors', 0);
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_PATH', dirname(dirname(ROOT_PATH)).'/');
define('CACHE_PATH', ROOT_PATH.'cache/');
define('CONFIG_PATH', ROOT_PATH.'config/');
require HF_PATH.'class_loader/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$errorHandler = new CommandErrorHandler;
$errorHandler->run();
$parser = new CommandParser;
$parser->parse();