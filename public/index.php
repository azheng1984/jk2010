<?php
//$x = microtime(true);
//for ($i =0 ; $i < 1000000; ++$i) {
//}
//echo (microtime(true) - $x) * 1000;
//throw $y;
require dirname(__DIR__) . '/vendor/autoload.php';
Hyperframework\Web\App::run();
