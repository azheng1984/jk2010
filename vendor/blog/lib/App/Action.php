<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbImportCommand;
use Hyperframework\Db\DbProfiler;
use Hyperframework\WebClient;
use PDO;

class Action {
    public function before() {
        CsrfProtection::run();

$time_start = microtime(true);

//for ($i = 0; $i < 10; ++$i) {
//    WebClient::sendAll(array('http://www.google.com.hk/'), function ($req, $res){
//        print_r($res);
//        $req['client']->close();
//    });
//}

//echo 'no share sid';
$client = new WebClient;
//$s = curl_share_init();
//curl_share_setopt($s, CURLSHOPT_UNSHARE, CURL_LOCK_DATA_SSL_SESSION);
//$client->setOption(CURLOPT_SHARE, $s);
$p = true;
$client->setOptions(array(
    CURLOPT_HEADER => 1,
    CURLINFO_HEADER_OUT => 1,
    CURLOPT_POST => true,
    CURLOPT_COOKIE => 'hi',
    CURLOPT_COOKIE => null,
    CURLOPT_POSTFIELDS => null,
//    CURLOPT_POSTFIELDS => array(
//        'name' => 'hi',
//        'file[0]' => curl_file_create('/home/az/Desktop/sd.fie28932duiru', null),
//        'file[1]' => curl_file_create('/home/az/Desktop/sd.fie28932duiru', null)),
    CURLOPT_HTTPHEADER => array(
//        'Content-Type: applicatoin/json'
//        'Content-Type: application/x-www-form-urlencoded',
//        'Content-Length:5',
    ),
//    array('application/json' => 'sdsdfdf'),
//    array('multipart/form-data' => array(
//        'file' => array('mime' => 'pdf', 'name' => 'lil', 'type' => 'file')
//    ));
    CURLOPT_READFUNCTION => function($h, $b, $c) use(&$p) {
    //return;
    var_dump('hi');
    echo $c;
    if ($p === true) {
        $p = false;
        return 'hi=hi';
    }
    },CURLOPT_WRITEFUNCTION => function($h, $x) {
        echo $x;
        return strlen($x);
    },
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
        var_dump($client->post('http://localhost/index.php?b=1'));
    //};
//    ob_flush();
}
$info = $client->getInfo();
//echo $info['request_header'];
print_r($info);
//$client->close();
//
//$time_end = microtime(true);
//$time = $time_end - $time_start;
//echo "Did in $time seconds\n";
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
