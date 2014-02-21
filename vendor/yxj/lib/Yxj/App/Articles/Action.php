<?php
namespace Yxj\App\Articles\Item;

class Action extends \Yxj\Action\ArticleAction {
    public function delete() {
        //bind id
        DbArticle::delete($articleId);
    }
}
