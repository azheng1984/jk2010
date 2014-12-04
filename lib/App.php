<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\App as Base;

class App extends Base {
    protected function initializeRouter() {
//        trigger_error('hi', E_USER_ERROR);
        dsfsaf();
        $this->setRouter(new Router($this));
    }
}
