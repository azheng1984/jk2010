<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;
use Hyperframework\WebClient\Curl;

class IndexController extends Controller {
    public function __construct($app) {
        parent::__construct($app);
        $this->addAroundFilter(':hi');
        $this->addAfterFilter(':hi2');
        $this->addAroundFilter(function() {
            try {
                echo 'love in';
                yield;
                echo 'love out';
            } catch (\Exception $e) {
               throw $e;
            }
        });
        $this->addAfterFilter(':hi3', ['prepend' => true, 'actions' => 'delete']);
//        $this->removeFilter(':hi');
    }

    protected function hi() {
        $curl = new Curl;
        echo Curl::sendAll(['http://www.baidu.com/', 'http://www.baidu.com/'], function($ctx) {
            $ctx['request'];
            $ctx['client'];
            print_r($ctx);
        }, null, ['request_fetching_callback' => function() {
            if (isset($this->x) === false) {
                $this->x = 0;
            }
            usleep(10);
            $this->x++;
//            flush();
            if ($this->x < 1000) {
                return;
            }
            return false;
        }]);
        echo Curl::sendAll(['http://www.baidu.com/'], function($ctx) {
            print_r($ctx);
        });
        echo 'in!!!';
        yield;
        echo 'out!!!';
    }

    protected function hi2() {
        echo 'after xxxxxxxxxx';
    }

    protected function hi3() {
        echo 'after2';
    }
}
