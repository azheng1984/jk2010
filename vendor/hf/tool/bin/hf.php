<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_CACHE_PATH', ROOT_PATH.'cache/vendor/hf/');
require ROOT_PATH.'vendor/hf/lib/ClassLoader.php';
$classLoader = new ClassLoader;
$classLoader->run();
$hf = new Hf;
$hf->run($argv, $argc);