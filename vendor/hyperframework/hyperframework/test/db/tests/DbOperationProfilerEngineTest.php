<?php
namespace Hyperframework\Db;

use stdClass;
use Hyperframework\Common\Config;
use Hyperframework\Db\Test\DbOperationProfileHandler;
use Hyperframework\Db\Test\CustomLogger;
use Hyperframework\Logging\Logger;
use Hyperframework\Db\Test\TestCase as Base;

class DbOperationProfilerEngineTest extends Base {
    private $statement;
    private $connection;
    private $sql = 'SELECT * FROM Document';
    private $profilerEngine;

    protected function setUp() {
        parent::setUp();
        DbClient::connect('backup');
        $this->connection = DbClient::getConnection();
        $this->statement = DbClient::prepare($this->sql);
        Config::set('hyperframework.db.operation_profiler.enable', true);
        Config::set('hyperframework.db.operation_profiler.enable_logger', false);
        $this->profilerEngine = new DbOperationProfilerEngine;
    }

    protected function tearDown() {
        $this->deleteAppLogFile();
        parent::tearDown();
    }

    public function testTransactionProfile() {
        $this->mockProfileHandler(function(array $profile) {
            return 'backup' === $profile['connection_name']
                && 'begin' === $profile['transaction']
                && isset($profile['start_time'])
                && isset($profile['running_time']);
        });
        $this->profilerEngine->onTransactionOperationExecuting(
            $this->connection, 'begin'
        );
        $this->profilerEngine->onTransactionOperationExecuted();
    }

    public function testSqlStatementExecutionProfile() {
        $this->mockProfileHandler(
            function(array $profile) {
                return 'backup' === $profile['connection_name']
                    && $this->sql === $profile['sql']
                    && isset($profile['start_time'])
                    && isset($profile['running_time']);
            }
        );
        $this->profilerEngine->onSqlStatementExecuting(
            $this->connection, $this->sql
        );
        $this->profilerEngine->onSqlStatementExecuted();
    }

    public function testPreparedStatementExecutionProfile() {
        $this->mockProfileHandler(
            function(array $profile) {
                return 'backup' === $profile['connection_name']
                    && $this->sql === $profile['sql']
                    && isset($profile['start_time'])
                    && isset($profile['running_time']);
            }
        );
        $this->profilerEngine->onPreparedStatementExecuting($this->statement);
        $this->profilerEngine->onPreparedStatementExecuted();
    }

    public function testProfileHandlerClassConfig() {
        Config::set(
            'hyperframework.db.operation_profiler.profile_handler_class',
            'Hyperframework\Db\Test\DbOperationProfileHandler'
        );
        $this->assertTrue(
            $this->profilerEngine->getProfileHandler()
                instanceof DbOperationProfileHandler
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidProfileHandlerClassConfig() {
        Config::set(
            'hyperframework.db.operation_profiler.profile_handler_class', 'Unknown'
        );
        $this->profilerEngine->getProfileHandler();
    }

    public function testLogProfile() {
        Config::set(
            'hyperframework.db.operation_profiler.enable_logger', true
        );
        Config::set(
            'hyperframework.logging.log_level', 'DEBUG'
        );
        $this->profilerEngine->onSqlStatementExecuting(
            $this->connection, $this->sql
        );
        $this->profilerEngine->onSqlStatementExecuted();
        $this->assertTrue(file_exists(
            Config::getAppRootPath() . '/log/app.log'
        ));
    }

    public function testCustomLogger() {
        Config::set(
            'hyperframework.db.operation_profiler.enable_logger', true
        );
        Config::set(
            'hyperframework.db.operation_profiler.logger_class',
            'Hyperframework\Db\Test\CustomLogger'
        );
        $this->profilerEngine->onSqlStatementExecuting(
            $this->connection, $this->sql
        );
        $this->profilerEngine->onSqlStatementExecuted();
        $this->assertNotNull(CustomLogger::getLog());
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidCustomLoggerClassConfig() {
        Config::set(
            'hyperframework.db.operation_profiler.enable_logger', true
        );
        Config::set(
            'hyperframework.db.operation_profiler.logger_class', 'Unknown'
        );
        $this->profilerEngine->onSqlStatementExecuting(
            $this->connection, $this->sql
        );
        $this->profilerEngine->onSqlStatementExecuted();
    }

    private function mockProfileHandler($handleCallback) {
        $mock = $this->getMock('Hyperframework\Db\Test\DbOperationProfileHandler');
        $this->profilerEngine->setProfileHandler($mock);
        return $mock->expects($this->once())->method('handle')
            ->will($this->returnCallback($handleCallback));
    }
}
