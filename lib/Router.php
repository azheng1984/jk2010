<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Router as Base;

class Router extends Base {
    protected function execute() {
        if ($this->match('/')) return;
        if ($this->match('docs')) return 'docs/show';
        if ($this->match('docs/*name')) return 'docs/show';
        if ($this->match('blog')) return 'blog/index';
    }
}
