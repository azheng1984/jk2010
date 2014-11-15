<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;

class IndexController extends Controller {
    public function __construct($app) {
        parent::__construct($app);
        $this->addAroundFilter(':hi');
        $this->addAroundFilter(function($controller) {
            echo 'love in';
            yield;
            echo 'love out';
        });
        $this->addAfterFilter(':hi2');
        $this->addAfterFilter(':hi2');
    }

    protected function hi() {
        echo 'in';
        yield;
        echo 'out';
    }

    protected function hi2() {
        echo 'after';
    }
}
