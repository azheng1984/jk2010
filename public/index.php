<?php
//$x = microtime(true);
//for ($i =0 ; $i < 1000000; ++$i) {
//}
//echo (microtime(true) - $x) * 1000;
//Config
var_dump(file_get_contents('php://input'));
var_dump(file_get_contents('php://input'));
exit;
require dirname(__DIR__) . '/vendor/autoload.php';
Hyperframework\Web\App::run();
