<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\App as Base;

class App extends Base {
    protected function initializeRouter() {
        $this->setRouter(new Router($this));
    }
}
