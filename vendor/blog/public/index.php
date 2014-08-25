<?php
namespace Hyperframework\Blog;

$fp = fopen("php://output", 'w');
var_dump($fp);
$fp = fopen("php://output", 'w');
var_dump($fp);                   
$fp = fopen("php://output", 'w');
fclose($fp);
var_dump($fp);
exit;
//fwrite($fp, "hello");
//echo 'hi';
////fclose($fp);
//exit;
//$x= array();
//$x[1] = 'hi';
//$x[0] = 'hi2';
//unset($x[1]);
//$x[1] = 'hi3';
//var_dump($x);
//$fp   = fopen('data://text/plain;base64,', 'r');
//var_dump(fclose($fp));
//var_dump(STDOUT);
$x = curl_init();
//curl_setopt($x, CURLOPT_CUSTOMREQUEST, null);
$f = fopen('php://output', 'w');
curl_setopt($x, CURLOPT_FILE, $f);
//curl_setopt($x, CURLOPT_WRITEFUNCTION, function($x, $y) {
//    echo $y;
//    return strlen($y);
//});
//exit;
//curl_setopt($x, CURLOPT_HTTP200ALIASES, null);
curl_setopt($x, CURLOPT_URL,  'http://www.baidu.com/');
//curl_setopt($x, CURLOPT_STDERR, null);
//curl_setopt($x, CURLOPT_WRITEHEADER, null);
//curl_setopt($x, CURLOPT_INFILE, null);
//
////var_dump(curl_setopt($x, CURLOPT_RETURNTRANSFER, true));
//curl_setopt($x, CURLOPT_POSTFIELDS, null);
//var_dump(curl_setopt($x, CURLOPT_HTTPGET, true));
////curl_setopt($x, CURLOPT_HEADER, true);
//curl_setopt($x, CURLOPT_URL,  'http://www.baidu.com/');
curl_exec($x);
//var_dump(curl_getinfo($x));
//
//fclose($f);
exit;
use Hyperframework\Web\Runner;
define('Hyperframework\Blog\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'init_const.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Runner.php';
Runner::run(__NAMESPACE__, ROOT_PATH);
