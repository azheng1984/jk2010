<?php
namespace Hyperframework\Blog;

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
echo file_get_contents('php://input');
    print_r($_POST);
    print_r($_FILES);
    echo $_SERVER['REQUEST_METHOD'];
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
<form method="POST" enctype="multipart/form-data" action="?b=1">
<input type="checkbox" name ="hi" value="1"/>
<input type="checkbox" name ="hi" value="2"/>
<input type="file" name='f'/>
<input type="submit" />
</form>
