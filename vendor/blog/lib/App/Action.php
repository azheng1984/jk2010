<?php
namespace Hyperframework\Blog\App;

use Hyperframework\ValidationException;

class Action {
    public function post($ctx) {
        try {
            $article = $ctx->getForm('article');
            Article::save($article);
            $ctx->redirect('/articles/' . $article['id']);
        } catch (ValidationException $e) {
            return ['article' => $article, 'errors' => $e->getErrors()];
        }
    }
}
