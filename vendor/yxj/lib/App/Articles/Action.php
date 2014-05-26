<?php
namespace Yxj\App\Articles;

class Action extends \Yxj\Actions\ArticleAction {
    public function before() {
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
