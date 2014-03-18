<?php
namespace Yxj\App\Articles\Item\Edit;

use Yxj\BaseAction;

class Action extends BaseAction {
    public function put() {
        return parent::save();
    }
}

// http://taobao.com/articles/12345
// http://www.taobao.com/
