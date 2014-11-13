<?php
namespace _;
$p = 'aticles/#:id';
$key = ':id';
$value = '[0-9]+';
$p = preg_replace(
    '#\{?\#:([0-9a-zA-Z]+)\}?#',
    '(?<\1>[^/]+?)',
    $p
);

//function hi($controller) {
//    echo 'start out';
//    if (false) {
//        echo 'start';
//        yield;
//        echo 'end';
//    }
//    yield;
//    echo 'end out';
//}

//$x = hi('x');
//var_dump($x->valid());
//var_dump($x->next());

//foreach (hi() as $item) {
//    echo $item;
//    if ($item === 2) {
//        throw new \Exception;
//    }
//}

class _ {
    public function __construct() {
//        echo 'hi';
    }

    public function name() {
        $this->xx('xx');
    }

    private function xx($param) {
        echo 'yy';
    }
}
class index extends _{
   protected function xx($param) {
       echo 'xx';
   }
}

//(new index)->name();
namespace Hyperframework\Blog; //$x = array();
//echo $p;
//exit;

//echo $x->hi;

//print_r(opcache_get_status("/home/az/quickquick/config/init.php"));

//$s = microtime(true);
////$x = array();
//$x = array();
//for ($i = 0; $i < 1000000; ++$i) {
//    gettype($x);
////if (preg_match('#^(?<name>[0-9a-z]+)(?<name2>(?<name4>(?<hello>[0-9A-Z]))+)#', 'xxxxxxxxxxxxdddd', $matches)) {
////    //print_r($matches);
////}
////if (preg_match('#^([0-9a-z]+)((([0-9A-Z]))+)#', 'xxxxxxxxxxxxdddd', $matches)) {
////    //print_r($matches);
////}
////if (preg_match('#^([0-9a-z]+)#', 'xxx', $matches)) {
////    //print_r($matches);
////}
////    $x = [0 => $i];
//    //$x[0] = $i;
//    //strpos($name, '|');
//    //explode('|', $name);
//    //$name = str_replace(' | ', '|', $name);
//    //preg_match('/^[a-zA-Z0-9-|]+$/', $name);
//    //preg_match('/^([a-zA-Z0-9-|]|( \| ))+$/', $name);
//    //exit;
//    //preg_match('/^([a-zA-Z0-9-|]|( \| ))+$/', $name);
//    //isset($x[0]);
////    array_key_exists(0, $x);
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

//use Hyperframework\Web\Runner;
define('Hyperframework\Blog\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'init_const.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Runner.php';
require ROOT_PATH . DIRECTORY_SEPARATOR . 'lib'
    . DIRECTORY_SEPARATOR . 'Runner.php';
Runner::run(__NAMESPACE__, ROOT_PATH);
//throw new \Exception
//trigger_error('xx', E_USER_ERROR);
return function() { ?>
<form method="get" enctype="application/x-www-form-urlencoded" action="#sdf?q=s#2233">
<input type="checkbox" name ="hi" value="9"/>
<input type="checkbox" name ="hi" value="10"/>
</form>
<?php };

if ($this->isMediaType('html')) {
    return;
}

if ($this->isMediaType('json')) {
}

switch ($this->getViewFormat()) {
    case 'json':
        return new Xml;
    case 'media':
        return $this->getXmlData();
    case 'rss':
        return $this->getRssData();
}

$this->bindRender([
    'json' => function() {
        $this->renderJson();
        $this->disableView();
        return;
    },
    'bai' => function() {
        $this->renderJson();
    }
]);

$this->renderJson(function() {
});
$this->renderXml(function() {
});
$this->render();


