<?php
namespace Hyperframework\Blog;
$x = curl_init();
//echo (string)array();
if (isset($_GET['b'])) {
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
