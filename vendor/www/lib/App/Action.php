<?php
namespace Hft;

use Hft\Models\Article;
use Hyperframework\ValidationException;
use Hyperframework\Web\NotFoundException;

class Action {
    public function get($app) {
        $article = Article::getRowById($app->getParams('id'));
        if ($article === null) {
            throw new NotFoundException;
        }
        return array('article' => $article);
    }

    public function patch($app) {
        return $this->save($app);
    }

    public function post($app) {
        return $this->save($app);
    }

    private function save($app) {
        $article = FormFilter::run('article');
        try {
            Article::save($article);
            $app->redirect('/articles/' . $article['id']);
        } catch (ValidationException $e) {
            return array('article' => $article, 'errors' => $e->getErrors());
        }
    }
}
