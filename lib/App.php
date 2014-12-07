<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\App as Base;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbTransaction;

class App extends Base {
    protected function createRouter() {
        DbTransaction::run(function() {
            DbClient::findAll('select * from Article');
        });
        return new Router($this);
    }
}
