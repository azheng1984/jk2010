<?php
namespace Yxj\App\Articles\Item;

class Action extends \Yxj\Actions\ArticleAction {
    public function before() {
    }

    public function delete() {
        //bind id
        DbArticle::delete($articleId);
    }
}
