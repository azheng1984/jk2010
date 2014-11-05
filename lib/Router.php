<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Router as Base;

class Router extends Base {
    public function parse() {
        $this->match('/');
        $this->matchResource('article');
        $this->matchScope('article', function() {
            echo $this->getPath();
            $this->match('*path');
        });
        $this->match('(:module(/:controller(/:action)))', [':id' => '[0-9]+']);
        $this->match('article/:id(/*comments)', [':id' => '[0-9]+', 'formats' => 'jpg']);
        $this->match('article/:id(/*comments)', [':id' => '[0-9]+']);
    }
}
