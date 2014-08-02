<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;

class Action {
    public function get() {
        echo 'hi';
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
