<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;

class Action {
    public function before() {
        CsrfProtection::run();
        print_r(DbClient::getRowById('Article', 3));
    }

    public function after($ctx) {
        echo 'xx';
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
