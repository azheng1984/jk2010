<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Router as Base;

class Router extends Base {
    public function parse() {
        $this->match('/article/:id(/*comments)', [':id' => '[0-9]+', 'formats' => ['default' => 'jpg']]);
        $this->match('/article/:id(/*comments)', [':id' => '[0-9]+']);
    }
}
