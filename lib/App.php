<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\App as Base;

class App extends Base {
    public function initializePath() {
        $router = new Router($this);
        echo $router->run();
        exit;
    }
}
