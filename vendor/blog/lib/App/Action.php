<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;

class Action {
    public function patch($ctx) {
        $id = $ctx->getParam('id');
        $article = $ctx->getForm('article');
        if (Article::isValid($article, $errors) === false) {
            return compact('article', 'errors');
        }
        Article::save($article);
        $ctx->redirect('/articles/' . $article['id']);
    }
}
