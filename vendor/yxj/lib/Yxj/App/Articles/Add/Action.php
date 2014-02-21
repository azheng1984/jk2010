<?php
namespace Yxj\App\Article\New;

class Action extends \Yxj\Action\ArticleAction {
    public function post() {
        return parent::save();
    }
}
