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
        echo $curl->asyncSend([
            'requests' => ['www.baidu.com/', 'www.baidu.com/'],
            'on_complete' => function($ctx) {
                $ctx['request'];
                $ctx['client'];
                print_r($ctx);
            },
            'request_fetching_callback' => function() {
                if (isset($this->x) === false) {
                    $this->x = 0;
                }
                echo 'ok!' . $this->x;
                usleep(100000);
                $this->x++;
                if ($this->x < 10) {
                    return 'www.baidu.com';
                }
                return false;
            }
        ]);
//        echo Curl::sendAll(['www.baidu.com/'], function($ctx) {
//            print_r($ctx);
//        });
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
