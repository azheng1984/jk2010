<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Router as Base;

class Router extends Base {
    protected function execute() {
        if ($this->match('/')) {
            $this->redirect('/cn');
        }
        if ($this->match('cn')) return;
        if ($this->match('cn/docs')) return 'docs/show';
        if ($this->match('cn/license')) return 'license/show';
        if ($this->match('cn/docs/*name')) return 'docs/show';
        if ($this->match('cn/blog')) return 'blog/show';
    }
}
