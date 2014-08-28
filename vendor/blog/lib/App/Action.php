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

echo 'sa';
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

$client->setOptions(array(
    CURLOPT_HTTPHEADER => array(
        'Cookie: x',
        'Accept:',
    ),
    CURLINFO_HEADER_OUT => 1,
    CURLOPT_HEADER => 1,
    CURLOPT_COOKIE => 'hi',
    CURLOPT_POSTFIELDS=> 'hi=%sdi+1:',
));

//array('xml' => 'dfasdf');
//array('json' => 'dfasdf');
//array('form-data' => 'dfasdf');
//array('form-multi' => 'dfasdf');

for ($i = 0; $i < 1; ++$i) {
    //echo '.';
    //if (strlen($r = ) === 0) {
    //    echo  $r;
        $client->get('http://sh.daoxila.com/');
    //};
//    ob_flush();
}
print_r($client->getInfo());
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
