<?php
namespace Yxj\App\Article\Add;

class Action extends \Yxj\Action\ArticleAction {
    public function post() {
        return parent::save();
    }
}
