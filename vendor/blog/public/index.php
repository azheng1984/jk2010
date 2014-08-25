<?php
namespace Hyperframework\Blog;

$fp = fopen("php://output", 'w');
fwrite($fp, "hello1");
$fp2 = fopen("php://output", 'w');
fwrite($fp2, "hello2");
fwrite($fp, "hello3");
$x = array();
    $fp3 = fopen("php://output", 'w');
for ($i= 0; $i < 100000; ++$i) {
    $x[] = fopen("php://output", 'w');
}
function convertToBytes($memoryLimit)
    {
        if ('-1' === $memoryLimit) {
            return -1;
        }

        $memoryLimit = strtolower($memoryLimit);
        $max = strtolower(ltrim($memoryLimit, '+'));
        if (0 === strpos($max, '0x')) {
            $max = intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = intval($max, 8);
        } else {
            $max = intval($max);
        }

        switch (substr($memoryLimit, -1)) {
            case 't': $max *= 1024;
            case 'g': $max *= 1024;
            case 'm': $max *= 1024;
            case 'k': $max *= 1024;
        }

        return $max;
    }
echo '<br>' . sprintf('%.1f MB', memory_get_peak_usage(true) / 1048576);
//fwrite(STDERR, "...");
//fwrite(STDOUT, 'hi..x.');
fwrite($fp3, PHP_SAPI);
fclose($fp3);
//error_log('hello!!!!');
var_dump($fp);
exit;
fwrite($fp, "hello");
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
