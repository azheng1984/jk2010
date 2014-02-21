<?php
namespace Yxj\App\Articles\Item\Edit;

class Action extends \Yxj\Action\ArticleAction {
    public function put() {
        return parent::save();
    }
}

// http://taobao.com/article/12345
// http://www.taobao.com/
