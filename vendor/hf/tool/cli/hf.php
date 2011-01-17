<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_PATH', dirname(ROOT_PATH).'/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/');
require HF_PATH.'lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();

