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
//        var_dump(curl_version());
        CsrfProtection::run();
        $a = curl_init();
        $b = curl_init();
        $sh = curl_share_init();

curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_COOKIE);
//curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_SSL_SESSION);

$ch1 = curl_init("http://www.iteye.com/");
curl_setopt($ch1, CURLOPT_SHARE, $sh);
curl_setopt($ch1, CURLINFO_HEADER_OUT, true);
curl_setopt($ch1, CURLOPT_HEADER, true);
curl_setopt($ch1, CURLOPT_COOKIEFILE, '/tmp/c.txt');
curl_setopt($ch1, CURLOPT_COOKIEJAR, '/tmp/c.txt');
curl_exec($ch1);

$ch2 = curl_init("http://www.daoxila.com/");
curl_setopt($ch2, CURLOPT_SHARE, $sh);
curl_setopt($ch2, CURLINFO_HEADER_OUT, true);
curl_exec($ch2);

print_r(curl_getinfo($ch1));
print_r(curl_getinfo($ch2));

//$time_start = microtime(true);
//for ($i = 0; $i < 100; ++$i) {
//    WebClient::sendAll(array('https://i.mi.com/'), function ($req, $res){
//        echo strlen($res['content']) . '<br>';
//        $req['client']->close();
//    });
//}
//echo 'sa';

//echo 'no share sid';
//$client = new WebClient;
//$s = curl_share_init();
//curl_share_setopt($s, CURLSHOPT_UNSHARE, CURL_LOCK_DATA_SSL_SESSION);
//$client->setOption(CURLOPT_SHARE, $s);
//for ($i = 0; $i < 20000; ++$i) {
//    echo '.';
//    if (strlen($client->get('https://i.mi.com/')) === 0) {
//        echo  'x';
//    };
//    ob_flush();
//}
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
