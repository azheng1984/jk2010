<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Db\Test\TestCase as Base;

class DbProfilerTest extends Base {
    private $statement;
    private $connection;

    protected function setUp() {
        parent::setUp();
        $this->connection = DbClient::getConnection();
        $this->statement = DbClient::prepare('SELECT * FROM Document');
        Config::set('hyperframework.db.profiler.enable', true);
    }

    protected function tearDown() {
        DbProfiler::setProfileHandler(null);
        parent::tearDown();
    }

    public function testIsEnabled() {
        $this->assertTrue(DbProfiler::isEnabled());
        Config::set('hyperframework.db.profiler.enable', false);
        $this->assertFalse(DbProfiler::isEnabled());
    }

    public function testTransactionProfile() {
        DbProfile::onTransactionOperationExecuting('');
    }

    public function testConnectionExecutionProfile() {
    }

    public function testStatementExecutionProfile() {
    }

    public function testProfileHandlerClassConfig() {
    }

    public function testInvalidProfileHandlerClassConfig() {
    }

    public function testLogProfile() {
    }

    public function testCustomLogger() {
    }

    public function testInvalidCustomLogger() {
    }
}
