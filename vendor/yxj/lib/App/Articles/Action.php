<?php
namespace Yxj\App\Articles;

use Hyperframework\Web\Application;

class Action extends \Yxj\Actions\ArticleAction {
    public function before($app) {
        $articleId = $app->getParam('id');
        $app->redirect();
        PathContext::get('id');
        Application::set();
        $this->params['id'];
        $app->getParams('id');
        $ctx->getParams('id');
        $articleId = $_GET('#id');
        $actionResult = $app->getActionResult();
    }

    public function post($app) {
        $id = $app->getParam('id');
        $categoryId = $app->getParam('id_0');
        $article = new ArticleInputMapper::execute();
        if ($article !== null) {
            Db::insert('article', $article);
        }
        return ArticleInputMapper::getErrors();
    }

    public function delete($app) {
        $articleId = $app->getParam('id');
        $id = InputMapper::get('id', 'int');
        if (DbArticle::delete($id)) {
        }
    }
}
