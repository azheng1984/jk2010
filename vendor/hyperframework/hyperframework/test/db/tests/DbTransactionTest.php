<?php
namespace Hyperframework\Db;

use Hyperframework\Db\Test\TestCase as Base;

class DbTransactionTest extends Base {
    public function testRun() {
        $this->assertFalse(DbClient::inTransaction());
        DbTransaction::run(function() {
            $this->assertTrue(DbClient::inTransaction());
        });
        $this->assertFalse(DbClient::inTransaction());
    }
}
