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
        $client = new WebClient;
        $client->setOption(CURLOPT_HEADER, true);
        $client->setOption(CURLINFO_HEADER_OUT, true);
        //$client->setOption(CURLOPT_NETRC, null);
        $client->setOption(CURLOPT_ENCODING, null);
        $client->get('http://zhidao.baidu.com/', array(CURLOPT_ENCODING => 0));
        print_r($client->getInfo());
        $client->get('http://zhidao.baidu.com/');
        print_r($client->getInfo());
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
