<?php
namespace Yxj\App\Article;

class Action extends \Yxj\Action\ArticleAction {
    public function delete() {
        //bind id
        DbArticle::delete($articleId);
    }
}
