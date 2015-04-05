<?php
namespace Hyperframework\Db;

use stdClass;
use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\TestCase as Base;
use Hyperframework\Db\Test\DbOperationProfileHandler;

class DbOperationProfilerTest extends Base {
    protected function setUp() {
        parent::setUp();
        Config::set('hyperframework.db.operation_profiler.enable', true);
    }

    public function testIsEnabled() {
        $this->assertTrue(DbOperationProfiler::isEnabled());
        Config::set('hyperframework.db.operation_profiler.enable', false);
        $this->assertFalse(DbOperationProfiler::isEnabled());
    }

    public function testOnTransactionOperationExecuting() {
        $connection = DbClient::getConnection(true);
        $operation = 'begin';
        $this->mockEngineMethod('onTransactionOperationExecuting')
            ->with($connection, $operation);
        DbOperationProfiler::onTransactionOperationExecuting($connection, $operation);
    }

    public function testOnTransactionOperationExecuted() {
        $this->mockEngineMethod('onTransactionOperationExecuted');
        DbOperationProfiler::onTransactionOperationExecuted();
    }

    public function testOnSqlStatementExecuting() {
        $connection = DbClient::getConnection(true);
        $sql = 'sql';
        $this->mockEngineMethod('onSqlStatementExecuting')
            ->with($connection, $sql);
        DbOperationProfiler::onSqlStatementExecuting($connection, $sql);
    }

    public function testOnSqlStatementExecuted() {
        $this->mockEngineMethod('onSqlStatementExecuted');
        DbOperationProfiler::onSqlStatementExecuted();
    }

    public function testonPreparedStatementExecuting() {
        $statement = DbClient::prepare('SELECT * FROM Document');
        $this->mockEngineMethod('onPreparedStatementExecuting')
            ->with($statement);
        DbOperationProfiler::onPreparedStatementExecuting($statement);
    }

    public function testOnPreparedStatementExecuted() {
        $this->mockEngineMethod('onPreparedStatementExecuted');
        DbOperationProfiler::onPreparedStatementExecuted();
    }

    public function testGetProfileHandler() {
        $this->mockEngineMethod('getProfileHandler')->willReturn(true);
        $this->assertTrue(DbOperationProfiler::getProfileHandler());
    }

    public function testSetProfileHandler() {
        $handler = new DbOperationProfileHandler;
        $this->mockEngineMethod('setProfileHandler')->with($handler);
        DbOperationProfiler::setProfileHandler($handler);
    }

    public function testGetDefaultEngine() {
        $this->assertInstanceOf(
            'Hyperframework\Db\DbOperationProfilerEngine',
            DbOperationProfiler::getEngine()
        );
    }

    public function testSetEngineUsingConfig() {
        Config::set(
            'hyperframework.db.operation_profiler.engine_class',
            'stdClass'
        );
        $this->assertInstanceof('stdClass', DbOperationProfiler::getEngine());
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidEngineConfig() {
        Config::set('hyperframework.db.operation_profiler.engine_class', 'Unknown');
        DbOperationProfiler::getEngine();
    }

    public function testSetEngine() {
        $engine = new stdClass;
        DbOperationProfiler::setEngine($engine);
        $this->assertSame($engine, DbOperationProfiler::getEngine());
        $this->assertSame(
            $engine, Registry::get('hyperframework.db.operation_profiler_engine')
        );
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMock('Hyperframework\Db\DbOperationProfilerEngine');
        DbOperationProfiler::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
