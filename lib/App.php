<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\App as Base;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbTransaction;

class App extends Base {
    protected function initializeRouter() {
//        trigger_error('hi', E_USER_ERROR);
//        dsfsaf();
        DbTransaction::run(function() {
            DbClient::findAll('select * from Article');
        });
        $this->setRouter(new Router($this));
    }
}
