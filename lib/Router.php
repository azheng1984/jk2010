<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Router as Base;

class Router extends Base {
    protected function execute() {
        if ($_SERVER['HTTP_HOST'] !== 'hyperframework.com' && $_SERVER['HTTP_HOST'] !== 'localhost') {
            header('Cache-Control: max-age=0, private, must-revalidate');
            $this->redirect('http://hyperframework.com' . $_SERVER['REQUEST_URI'], 301);
        }
        if ($this->match('/')) {
            header('Cache-Control: max-age=0, private, must-revalidate');
            $this->redirect('/cn', 301);
        }
        if ($this->match('cn')) return;
        if ($this->match('cn/docs')) return 'docs/show';
        if ($this->match('cn/license')) return 'license/show';
        if ($this->match('cn/manual(/*name)')) return 'manual/show';
        if ($this->match('cn/blog')) return 'blog/show';
        if ($this->match('cn/downloads')) return 'downloads/show';
    }
}
