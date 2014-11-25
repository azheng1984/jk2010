<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Router as Base;

class Router extends Base {
    protected function parse() {
        if ($this->matchResources('articles')) return;
        if ($this->matchScope(['xxx/:xxx_id', 'formats' => ['jpg']], function() {
            if ($this->match('/')) {
                return;
            }
            if ($this->matchResources('articles')) {
                print_r($this->getParams());
                echo 'matched!';
                return;
            }
        })) return;
        if ($this->matchScope(['xxx/:id', 'formats' => ['default' => 'jpg']], function() {
        })) return;
        if ($this->matchScope(['xxx/:id'], function() {
        })) return;
        if ($this->match('/')) return;
        if ($this->matchResources('articles/:article_id/comments')) return;
        return;

        $this->setMatchStatus(false);
        $this->matchScope('article', function() {
            echo $this->getPath();
            $this->match('*path');
        });
        $this->setMatchStatus(false);
        $this->match('(:module(/:controller(/:action)))', [':id' => '[0-9]+']);
        $this->setMatchStatus(false);
        $this->matchGet('(:module(/:controller(/:action)))', [':id' => '[0-9]+']);
        $this->setMatchStatus(false);
        $this->match('article/:id(/*comments)', [':id' => '[0-9]+', 'formats' => 'jpg']);
//      $this->matchPatch('article/:id(/*comments)', [':id' => '[0-9]+', 'formats' => 'jpg']);
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
