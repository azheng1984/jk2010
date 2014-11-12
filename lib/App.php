<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\App as Base;

class App extends Base {
    public function initializeRouter() {
        $this->addBeforeFilter('startTimer');
        $this->addAfterFilter('endTimer');
        $this->addAroundFilter([
            'before' => 'startTimer',
            'after' => 'endTimer',
            'after_throwing' => 'xxx'
        ],
            ['actions' => 'delete'],
            ['ignored_actions' => ['show', 'index', 'edit', 'new', 'search']]
        );
        $this->addAroundFilter(function($next) {
            ddd();
            $next();
            ddd();
        });
        $this->setRouter(new Router($this));
    }
}
