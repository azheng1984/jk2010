<?php
namespace Hyperframework\Blog\App;

use Hyperframework\ValidationException;

class Action {
    public function patch($ctx) {
        $ctx->filter(array());
    }

    public function post($ctx) {
        $article = $ctx->getForm('article');
        try {
            Article::save($article);
            $ctx->redirect('/articles/' . $article['id']);
        } catch (ValidationException $e) {
            return ['article' => $article, 'errors' => $e->getErrors()];
        }
    }


}
