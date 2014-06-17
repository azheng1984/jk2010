<?php
namespace Yxj\App\Articles;

class Action extends \Yxj\Actions\ArticleAction {
    public function before($app) {
        $articleId = $app->getParam('id');
        $app->redirect();
        PathContext::get('id');
        $this->params['id'];
        $articleId = $_GET('#id');
        $actionResult = $app->getActionResult();
    }

    public function post() {
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
