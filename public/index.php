<?php
namespace hi;

file_put_contents('/home/az/io/w', "<?phpecho'hello'");

//var_dump(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_XHMLT));
//var_dump(count(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML401)));
//var_dump(count(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5)));
//var_dump(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5));
//var_dump(htmlspecialchars("'", ENT_QUOTES | ENT_HTML401)); // string(6) "&#039;"
//var_dump(htmlspecialchars("'", ENT_QUOTES | ENT_HTML5));   // string(6) "&apos;"
//$s = microtime(true);
//for ($i = 0; $i < 1000000; ++$i) {
////    is_callable([$x, '__toString']);
//    var_dump(method_exists($x, '__toString'));
//}
//echo (microtime(true) - $s) * 1000;
//exit;
//$x = (string)false;
//function x()  {
//    throw new Exception;
//};
require 'hi.php';
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

use Hyperframework\Web\Runner;
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

require dirname(__DIR__) . '/vendor/autoload.php';
Runner::run();

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


