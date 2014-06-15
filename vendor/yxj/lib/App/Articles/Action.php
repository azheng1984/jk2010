<?php
namespace Yxj\App\Articles;

use Hyperframework\Web\RequestPath;

class Action extends \Yxj\Actions\ArticleAction {
    public function before($ctx) {
        $articleId = $ctx->get('id');
        PathContext::get('id');
        $this->params['id'];
        $articleId = $_GET('#id');
        ActionResult::get('article_id');
    }

    public function post() {
        $article = new ArticleInputMapper::execute();
        if ($article !== null) {
            Db::insert('article', $article); 
        }
        return ArticleInputMapper::getErrors();
    }

    public function delete() {
        $id = InputMapper::get('id', 'int');
        if (DbArticle::delete($id)) {
        }
    }
}
