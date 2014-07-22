<?php
namespace Hyperframework\Blog\App\Comments;

use Hyperframework\Blog\Modles\Article;

class Action {
    public function patch($ctx) {
        $article = $ctx->getForm('article');
        if (Article::isValid($article, $errors) === false) {
            return compact('article', 'errors');
        }
        Article::save($article);
        $ctx->redirect('/articles/' . $article['id']);
    }
}
