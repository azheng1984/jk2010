<?php
class index {
    private static $hi = 'hi';
   public static function name() {
       $c = function() {
           return self::$hi;
       };
//       $c->bindTo(null);
       return $c;
   }
}
$x = index::name();
echo $x();
exit;
require dirname(__DIR__) . '/vendor/autoload.php';
Hyperframework\Web\App::run();
