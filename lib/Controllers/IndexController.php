<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbConnectionManager;

class IndexController extends Controller {
    public function __construct($app) {
        //throw new \Exception;
        parent::__construct($app);
//        $this->addBeforeFilter('Hyperframework\Web\Controller');
        echo $this->getRouter()->getAction();
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

    protected function doShowAction() {
        DbClient::findById('Document', 1);
    }

    protected function hi() {
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
