<?php
//$x = microtime(true);
//for ($i =0 ; $i < 1000000; ++$i) {
//}
//echo (microtime(true) - $x) * 1000;
//$x = fopen(dirname(__DIR__) . '/log/app.log', 'a');
//var_dump(flock($x, LOCK_EX));
//exit;
require dirname(__DIR__) . '/vendor/autoload.php';
Hyperframework\Web\App::run();
