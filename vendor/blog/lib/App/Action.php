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
//for ($i = 0; $i < 100; ++$i) {
//    WebClient::sendAll(array('https://i.mi.com/'), function ($req, $res){
//        echo strlen($res['content']) . '<br>';
//        $req['client']->close();
//    });
//}
//echo 'sa';

//echo 'no share sid';
$client = new WebClient;
//$s = curl_share_init();
//curl_share_setopt($s, CURLSHOPT_UNSHARE, CURL_LOCK_DATA_SSL_SESSION);
//$client->setOption(CURLOPT_SHARE, $s);
for ($i = 0; $i < 10; ++$i) {
    echo '.';
    if (strlen($client->get('https://i.mi.com/')) === 0) {
        echo  'x';
    };
    ob_flush();
}
$client->close();
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
