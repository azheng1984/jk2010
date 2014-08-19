<?php
namespace Hyperframework\Blog;
//ob_start();
//ob_start('ob_gzhandler');
//echo 'sdf';
//var_dump(headers_list());
//ob_end_flush();
//
//var_dump(headers_list());
//header_remove();
//
//var_dump(headers_list());
//header("Content-Encoding: gzip");
//
//var_dump(headers_list());
//echo 'adfafsff';
//exit;
//测试低版本
//
class x {
    private static $x;

    public static function xx() {
        echo self::$x;
        $ref =& self::$x;
        $c = function() use (&$ref){
            $ref = 'hello';
        };
        return $c;
        //return self::$x;
    }
}
$v = x::xx();
$v();
x::xx();
exit;

use Hyperframework\Web\Runner;
define('Hyperframework\Blog\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'init_const.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Runner.php';
Runner::run(__NAMESPACE__, ROOT_PATH);
