#!/usr/bin/php
<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_PATH', dirname(dirname(ROOT_PATH)).'/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/');
define('HF_CONFIG_PATH', ROOT_PATH.'config/');
//print_r($argv);
$errorHanlder = new CommandErrorHandler;
$errorHanlder->run();
require HF_PATH.'class_loader/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$parser = new CommandParser(new CommandContext);
$parser->run();