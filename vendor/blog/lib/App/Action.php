<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbImportCommand;
use Hyperframework\Db\DbProfiler;
use Hyperframework\WebClient;
use PDO;

interface mi {

}
class obj implements mi, \ArrayAccess {
    private $container = array();
    public function __construct() {
        $this->container = array(
            "one"   => 1,
            "two"   => 2,
            "three" => 3,
        );
    }
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}

function xx(array $x) {
}

class Action {
    public function before() {
        CsrfProtection::run();
$time_start = microtime(true);
$path = '/home/az/zend/init autoloader.php';
function x(array $path) {
}
$x = new obj;
$x['hi'];
//current($x);

//echo end($x);
//xx($x);
var_dump(is_array($x));
$x = [];

var_dump($x instanceof \arrayaccess);
//for ($i = 0; $i < 10; ++$i) {
//    WebClient::sendAll(array('http://www.baidu.com/'), function ($req, $res){
//        print_r($res);
//        $req['client']->close();
//    });
//}

//echo 'no share sid';
$client = new WebClient;
//$s = curl_share_init();
//curl_share_setopt($s, CURLSHOPT_UNSHARE, CURL_LOCK_DATA_SSL_SESSION);
//$client->setOption(CURLOPT_SHARE, $s);

$client->post('http://localhost', array('file' => '/home/az/vim74/Filelist'));
echo $client->post('http://localhost?b=1', '@/home/az/vim74/Filelist');

exit;
$p = true;
$f = fopen('/home/az/vim74/Filelist', 'r');
$client->setOptions(array(
    CURLOPT_HEADER => 1,
    CURLOPT_INFILE => $f,
    CURLOPT_INFILESIZE => filesize('/home/az/vim74/Filelist'),
//    CURLOPT_WRITEHEADER => $f,
    CURLINFO_HEADER_OUT => 1,
    CURLOPT_UPLOAD => true,
    CURLOPT_POST => true,
//    CURLOPT_COOKIE => 'hi',
//    CURLOPT_COOKIE => null,
    CURLOPT_POSTFIELDS => null,
    CURLOPT_POSTFIELDS => array(
        'name' => 'hi',
        'file[0]' => curl_file_create('/home/az/Desktop/sd.fie28932duiru', null),
        'file[1]' => curl_file_create('/home/az/Desktop/sd.fie28932duiru', null)),
    CURLOPT_HTTPHEADER => array(
        'hi:hello'
//        'Content-Type: applicatoin/json'
//        'Content-Type: application/x-www-form-urlencoded',
//        'Content-Length:' . filesize('/tmp/xx.txt'),
    ),
//    array('application/json' => 'sdsdfdf'),
//    array('multipart/form-data' => array(
//        'file' => array('mime' => 'pdf', 'name' => 'lil', 'type' => 'file')
//    ));
//    CURLOPT_READFUNCTION => function($h, $b, $c) use(&$p) {
//      //var_dump($h);
//  //return;
//   var_dump('hi');
//   echo $c;
//   if ($p === true) {
//       $p = false;
//       return 'hi=hi';
//   }
//    },
//    CURLOPT_WRITEFUNCTION => function($h, $x) {
////echo $x;
//        return strlen($x);
//    },
//    CURLOPT_HEADERFUNCTION => function($h, $x) {
//
//        //var_dump($h);
//echo $x;
//        return strlen($x);
//    }
));

array('application/xml' => 'dfasdf');
array('application/json' => 'dfasdf');
array('multipart/form-data' => array(
));
array('' => 'dfasdf');

for ($i = 0; $i < 1; ++$i) {
    //echo '.';
    //if (strlen($r = ) === 0) {
    //    echo  $r;
        echo $client->post('http://localhost/index.php?b=1');
    //};
//    ob_flush();
}
$info = $client->getInfo();
var_dump( $client->getResponseHeaders());
//echo $info['request_header'];
print_r($info);
//$client->close();
//
//$time_end = microtime(true);
//$time = $time_end - $time_start;
//echo "Did in $time seconds\n";
$ch= curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://baidu.com');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('hi:hello'));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('hi2:hello'));

 curl_setopt($ch,    CURLINFO_HEADER_OUT, 1);

//curl_exec($ch);

//print_r(curl_getinfo($ch));
    }

    public function after($ctx) {
        //echo 'xx';
    }

    public function patch($ctx) {
        echo 'hello';
//        $article = $ctx->getForm('article');
//        if (Article::isValid($article, $errors) === false) {
//            return compact('article', 'errors');
//        }
//        Article::save($article);
//        $ctx->redirect('/articles/' . $article['id']);
    }
}
