<?php
namespace Hyperframework\Blog;
//the width of the biggest char @ 
$fontwidth = 11; 

//each chargroup has char-ords that have the same proportional displaying width 
$chargroup[0] = array(64); 
$chargroup[1] = array(37,87,119); 
$chargroup[2] = array(65,71,77,79,81,86,89,109); 
$chargroup[3] = array(38,66,67,68,72,75,78,82,83,85,88,90); 
$chargroup[4] = array(35,36,43,48,49,50,51,52,53,54,55,56,57,60,61,62,63, 69,70,76,80,84,95,97,98,99,100,101,103,104,110,111,112, 113,115,117,118,120,121,122,126); 
$chargroup[5] = array(74,94,107); 
$chargroup[6] = array(34,40,41,42,45,96,102,114,123,125); 
$chargroup[7] = array(44,46,47,58,59,91,92,93,116); 
$chargroup[8] = array(33,39,73,105,106,108,124); 
    
//how the displaying width are compared to the biggest char width 
$chargroup_relwidth[0] = 1; //is char @ 
$chargroup_relwidth[1] = 0.909413854; 
$chargroup_relwidth[2] = 0.728241563; 
$chargroup_relwidth[3] = 0.637655417; 
$chargroup_relwidth[4] = 0.547069272; 
$chargroup_relwidth[5] = 0.456483126; 
$chargroup_relwidth[6] = 0.36589698; 
$chargroup_relwidth[7] = 0.275310835; 
$chargroup_relwidth[8] = 0.184724689; 

//build fast array 
$char_relwidth = null; 
for ($i=0;$i<count($chargroup);$i++){ 
    for ($j=0;$j<count($chargroup[$i]);$j++){ 
        $char_relwidth[$chargroup[$i][$j]] = $chargroup_relwidth[$i]; 
    } 
} 

//get the display width (in pixels) of a string 
function get_str_width($str){ 
    global $fontwidth,$char_relwidth; 
    $result = 0; 
    for ($i=0;$i<strlen($str);$i++){ 
        $result += $char_relwidth[ord($str[$i])]; 
    } 
    $result = $result * $fontwidth; 
    return $result;    
} 
echo get_str_width('wdf');
//$x = array();
//var_dump(end($x));

//$s = microtime(true);
//$xxx = 'x';
//
//$x = array();
//for ($i = 0; $i < 1000000; ++$i) {
//    $x['x'] = $i;
//    //is_int($xxx);
////    preg_match('/^[a-zA-Z0-9_]+$/', 'INFO');
//    //rand();
//    //preg_match('/\w+/', 'INFO');
//    //strpos('INFOxxxxx', 'N');
//    //array();
//    //function() {};
//}
//echo (microtime(true) - $s) * 1000;
//exit;

if (isset($_GET['b'])) {
echo file_get_contents('php://input');
    print_r($_GET);
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
//throw new \Exception
//trigger_error('xx', E_USER_ERROR);
?>
<form method="get" enctype="application/x-www-form-urlencoded" action="#sdf?q=s#2233">
<input type="checkbox" name ="hi" value="9"/>
<input type="checkbox" name ="hi" value="10"/>

<input type="submit" />
</form>
