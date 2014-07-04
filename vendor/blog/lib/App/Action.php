<?php
namespace Hyperframework\Blog\App;

use Hyperframework\ValidationException;

class Action {
    public function patch($ctx) {
        $article = $ctx->filter(['content', 'title']);
        Article::updateFragment($article);
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
