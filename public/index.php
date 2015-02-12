<?php
//$x = microtime(true);
//for ($i =0 ; $i < 1000000; ++$i) {
//}
//echo (microtime(true) - $x) * 1000;
//$x = fopen(dirname(__DIR__) . '/log/app.log', 'a');
//var_dump(flock($x, LOCK_EX));
//exit;
//
class index {
    public static $name = 2;
    const Xx = 1;
    public static function name() {
        $name = 'xx42';
        return isset(self::$name);
    }

    public static function xx2($param) {
        return null;
    }
    
}
$x = 'Xx';
var_dump(index::name());
require dirname(__DIR__) . '/vendor/autoload.php';
Hyperframework\Web\App::run();
