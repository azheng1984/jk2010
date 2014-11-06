<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Router as Base;

class Router extends Base {
    public function parse() {
        $this->match('/');
        $this->setMatchStatus(false);
        $this->matchResource('article');
        $this->setMatchStatus(false);
        $this->matchScope('article', function() {
            echo $this->getPath();
            $this->match('*path');
        });
        $this->setMatchStatus(false);
        $this->match('(:module(/:controller(/:action)))', [':id' => '[0-9]+']);
        $this->match('GET (:module(/:controller(/:action)))', [':id' => '[0-9]+']);
        $this->setMatchStatus(false);
        $this->match('article/:id(/*comments)', [':id' => '[0-9]+', 'formats' => 'jpg']);
        $this->match('PUT|PATCH article/:id(/*comments)', [':id' => '[0-9]+', 'formats' => 'jpg']);
        $this->setMatchStatus(false);
        $this->match('article/:id(/*comments)', [':id' => '[0-9]+']);
exit;
//        if ($this->match('/')) return 'main/index/show';
//        if ($this->match('article/:id(/*comments)', [':id' => '[0-9]+']))
//            return 'comments/show';
//        if ($this->matchResource('article')) return;
//        if ($this->matchScope('main', function() {
//            echo $this->getPath();
//            $this->matchPost('*path');
//        })) {
//            $this->setModule('main');
//            return;
//        }
//        if ($this->matchGet('(:module(/:controller(/:action)))', [':id' => '[0-9]+'])) return;
//        if ($this->matchPost('article/:id(/*comments)', [':id' => '[0-9]+', 'formats' => 'jpg'])) return;
//        if ($this->matchDelete('article/:id(/*comments)', [':id' => '[0-9]+'])) return;

    }
}
