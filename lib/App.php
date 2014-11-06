<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\App as Base;

class App extends Base {
    public function initializeRouter() {
        $this->setRouter(new Router($this));
    }
}
