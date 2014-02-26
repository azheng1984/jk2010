<?php
namespace Yxj\App\Articles\Item\Edit;

class Action extends \Yxj\Action\ArticleAction {
    public function put() {
        return parent::save();
    }
}

// http://taobao.com/articles/12345
// http://www.taobao.com/
