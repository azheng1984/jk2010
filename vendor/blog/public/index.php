<?php
namespace Hyperframework\Blog;
@printf('xx%s');
//x($path);
//echo $path['hi'];
//exit;
//foreach ($path as $key => $value){
//echo 'hi';
//}
//        $handle = curl_init('file://' . $path);
//        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($handle, CURLOPT_NOBODY, true);
//        curl_setopt($handle, CURLOPT_HEADER, true);
//        $headers = curl_exec($handle);
//        echo $headers;
//        if ($headers === false) {
//            throw new Exception;
//            echo 'false';
//        }
//        curl_close($handle);
//        if (preg_match('/Content-Length: (\d+)/', $headers, $matches)) {
//            echo $matches[1];
//        }
//exit;
//echo PHP_INT_SIZE;
//$x = array();
//echo $x['xx'];
//var_dump( == null);
//exit;
//function x(array &$w = null) {
//    $w = 'hi';
//    var_dump($w);
//}
//$x = 2;
//x();
//return;

if (isset($_GET['b'])) {
//    header('http/1.1    204');
//    header(' ');
//var_dump(\getallheaders());
echo file_get_contents('php://input');
    print_r($_POST);
    print_r($_FILES);
    echo $_SERVER['REQUEST_METHOD'];
    exit;
}

if (isset($_GET['r'])) {
    if ($_GET['r'] < 10) {
       header('http/1.1 302');
       header('Location: http://localhost/?r='. ($_GET['r'] + 1));
    }
    header('http1.1/:1');
    echo $_GET['r'];
    exit;
}

use Hyperframework\Web\Runner;
define('Hyperframework\Blog\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'init_const.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Runner.php';
Runner::run(__NAMESPACE__, ROOT_PATH);
?>
<form method="get" enctype="application/x-www-form-urlencoded" action="#sdf?q=s#2233">
<input type="checkbox" name ="hi" value="9"/>
<input type="checkbox" name ="hi" value="10"/>

<input type="submit" />
</form>
