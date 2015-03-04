<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;
use Hyperframework\Web\ResponseHeaderHelper;
use Hyperframework\Db\DbClient;

class IndexController extends Controller {
    private function name(array $param) {
        return null;
    }

    private function name2($param) {
        return null;
    }

    public function __construct($app) {
        ResponseHeaderHelper::setCookie('xxname', null, ['path' => '/df']);
        ResponseHeaderHelper::setCookie('xname', 'value');
        DbClient::beginTransaction();
        DbClient::commit();
        //$this->name2();
        //$this->name(0);
        //throw new \Exception;
        parent::__construct($app);
//        $this->addBeforeFilter('Hyperframework\Web\Controller');
        echo $this->getRouter()->getAction();
        $this->addAroundFilter(function() {return $this->hi();});
        $this->addAfterFilter(function() {return $this->hi2();});
        $this->addAroundFilter(function() {
            try {
                echo 'love in';
                yield;
                echo 'love out';
            } catch (\Exception $e) {
               throw $e;
            }
        });
        $this->addAfterFilter(function() {$this->hi3();}, ['prepend' => true, 'actions' => ['delete']]);
    //        $this->removeFilter(':hi');
    }

    protected function doShowAction() {
        //echo 'hi';
        DbClient::findRowById('Document', 1);

        return 'x';
    }

    private function hi() {
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
