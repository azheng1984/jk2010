<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;

class IndexController extends Controller {
    public function __construct($app) {
        parent::__construct($app);
        $this->addAroundFilter(':hi');
        $this->addAfterFilter(':hi2');
        $this->addAroundFilter(function($controller) {
            try {
                echo 'love in';
                yield;
                echo 'love out';
            } catch (\Exception $e) {
                echo 'eat ex!!';
                throw $e;
            }
        });
        $this->addAfterFilter(':hi3');
    }

    protected function hi() {
        echo 'in';
        yield;
        echo 'out';
    }

    protected function hi2() {
        echo 'after xxxxxxxxxx';
    }

    protected function hi3() {
        echo 'after2';
        throw new \Exception;
    }
}
