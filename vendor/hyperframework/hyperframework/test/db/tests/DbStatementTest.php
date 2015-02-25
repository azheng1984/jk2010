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

    private function mockProfileHandler() {
        Config::set('hyperframework.db.profiler.enable', true);
        Config::set('hyperframework.db.profiler.enable_logger', false);
        $mock = $this->getMock('Hyperframework\Db\Test\ProfileHandler');
        $mock->expects($this->once())->method('handle');
        DbProfiler::setProfileHandler($mock);
    }
}
