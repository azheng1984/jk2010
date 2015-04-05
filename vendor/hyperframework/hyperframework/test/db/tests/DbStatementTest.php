<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Db\Test\TestCase as Base;

class DbStatementTest extends Base {
    public function testExecute() {
        $statement = DbClient::prepare('SELECT * FROM Document');
        $this->mockProfileHandler();
        $statement->execute();
    }

    public function testGetSql() {
        $sql = 'SELECT * FROM Document';
        $statement = DbClient::prepare($sql);
        $this->assertSame($sql, $statement->getSql());
    }

    public function testGetConnection() {
        $statement = DbClient::prepare('SELECT * FROM Document');
        $this->assertTrue($statement->getConnection() instanceof DbConnection);
    }

    private function mockProfileHandler() {
        Config::set('hyperframework.db.operation_profiler.enable', true);
        Config::set('hyperframework.db.operation_profiler.enable_logger', false);
        $mock = $this->getMock('Hyperframework\Db\Test\DbOperationProfileHandler');
        $mock->expects($this->once())->method('handle');
        DbOperationProfiler::setProfileHandler($mock);
    }
}
