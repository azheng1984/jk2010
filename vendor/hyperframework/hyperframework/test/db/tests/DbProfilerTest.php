<?php
namespace Hyperframework\Db;

use stdClass;
use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\TestCase as Base;
use Hyperframework\Db\Test\ProfileHandler;

class DbProfilerTest extends Base {
    protected function setUp() {
        parent::setUp();
        Config::set('hyperframework.db.profiler.enable', true);
    }

    public function testIsEnabled() {
        $this->assertTrue(DbProfiler::isEnabled());
        Config::set('hyperframework.db.profiler.enable', false);
        $this->assertFalse(DbProfiler::isEnabled());
    }

    public function testOnTransactionOperationExecuting() {
        $connection = DbClient::getConnection(true);
        $operation = 'begin';
        $this->mockEngineMethod('onTransactionOperationExecuting')
            ->with($connection, $operation);
        DbProfiler::onTransactionOperationExecuting($connection, $operation);
    }

    public function testOnTransactionOperationExecuted() {
        $this->mockEngineMethod('onTransactionOperationExecuted');
        DbProfiler::onTransactionOperationExecuted();
    }

    public function testOnSqlStatementExecuting() {
        $connection = DbClient::getConnection(true);
        $sql = 'sql';
        $this->mockEngineMethod('onSqlStatementExecuting')
            ->with($connection, $sql);
        DbProfiler::onSqlStatementExecuting($connection, $sql);
    }

    public function testOnSqlStatementExecuted() {
        $this->mockEngineMethod('onSqlStatementExecuted');
        DbProfiler::onSqlStatementExecuted();
    }

    public function testonPreparedStatementExecuting() {
        $statement = DbClient::prepare('SELECT * FROM Document');
        $this->mockEngineMethod('onPreparedStatementExecuting')
            ->with($statement);
        DbProfiler::onPreparedStatementExecuting($statement);
    }

    public function testOnPreparedStatementExecuted() {
        $this->mockEngineMethod('onPreparedStatementExecuted');
        DbProfiler::onPreparedStatementExecuted();
    }

    public function testGetProfileHandler() {
        $this->mockEngineMethod('getProfileHandler')->willReturn(true);
        $this->assertTrue(DbProfiler::getProfileHandler());
    }

    public function testSetProfileHandler() {
        $handler = new ProfileHandler;
        $this->mockEngineMethod('setProfileHandler')->with($handler);
        DbProfiler::setProfileHandler($handler);
    }

    public function testGetDefaultEngine() {
        $this->assertInstanceOf(
            'Hyperframework\Db\DbProfilerEngine',
            DbProfiler::getEngine()
        );
    }

    public function testSetEngineUsingConfig() {
        Config::set(
            'hyperframework.db.profiler.engine_class',
            'stdClass'
        );
        $this->assertInstanceof('stdClass', DbProfiler::getEngine());
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidEngineConfig() {
        Config::set('hyperframework.db.profiler.engine_class', 'Unknown');
        DbProfiler::getEngine();
    }

    public function testSetEngine() {
        $engine = new stdClass;
        DbProfiler::setEngine($engine);
        $this->assertSame($engine, DbProfiler::getEngine());
        $this->assertSame(
            $engine, Registry::get('hyperframework.db.profiler_engine')
        );
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMock('Hyperframework\Db\DbProfilerEngine');
        DbProfiler::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
