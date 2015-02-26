<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Db\Test\Profilehandler;
use Hyperframework\Db\Test\CustomLogger;
use Hyperframework\Logging\Logger;
use Hyperframework\Db\Test\TestCase as Base;

class DbProfilerTest extends Base {
    private $statement;
    private $connection;
    private $sql = 'SELECT * FROM Document';

    protected function setUp() {
        parent::setUp();
        DbClient::connect('backup');
        $this->connection = DbClient::getConnection();
        $this->statement = DbClient::prepare($this->sql);
        Config::set('hyperframework.db.profiler.enable', true);
        Config::set('hyperframework.db.profiler.enable_logger', false);
    }

    protected function tearDown() {
        DbProfiler::setProfileHandler(null);
        DbClient::setConnection(null);
        Logger::setLevel(null);
        $this->deleteAppLogFile();
        parent::tearDown();
    }

    public function testIsEnabled() {
        $this->assertTrue(DbProfiler::isEnabled());
        Config::set('hyperframework.db.profiler.enable', false);
        $this->assertFalse(DbProfiler::isEnabled());
    }

    public function testTransactionProfile() {
        $this->mockProfileHandler()->with(
            $this->callback(function(array $profile) {
                return 'backup' === $profile['connection_name']
                    && 'begin' === $profile['transaction']
                    && isset($profile['start_time'])
                    && isset($profile['running_time']);
            })
        );
        DbProfiler::onTransactionOperationExecuting($this->connection, 'begin');
        DbProfiler::onTransactionOperationExecuted();
    }

    public function testSqlStatementExecutionProfile() {
        $this->mockProfileHandler()->with(
            $this->callback(function(array $profile) {
                return 'backup' === $profile['connection_name']
                    && $this->sql === $profile['sql']
                    && isset($profile['start_time'])
                    && isset($profile['running_time']);
            })
        );
        DbProfiler::onSqlStatementExecuting($this->connection, $this->sql);
        DbProfiler::onSqlStatementExecuted();
    }

    public function testPreparedStatementExecutionProfile() {
        $this->mockProfileHandler()->with(
            $this->callback(function(array $profile) {
                return 'backup' === $profile['connection_name']
                    && $this->sql === $profile['sql']
                    && isset($profile['start_time'])
                    && isset($profile['running_time']);
            })
        );
        DbProfiler::onPreparedStatementExecuting($this->statement);
        DbProfiler::onPreparedStatementExecuted();
    }

    public function testProfileHandlerClassConfig() {
        Config::set(
            'hyperframework.db.profiler.profile_handler_class',
            'Hyperframework\Db\Test\Profilehandler'
        );
        $this->assertTrue(
            DbProfiler::getProfileHandler() instanceof ProfileHandler
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidProfileHandlerClassConfig() {
        Config::set(
            'hyperframework.db.profiler.profile_handler_class', 'Unknown'
        );
        DbProfiler::getProfileHandler();
    }

    public function testLogProfile() {
        Config::set(
            'hyperframework.db.profiler.enable_logger', true
        );
        Config::set(
            'hyperframework.logging.log_level', 'DEBUG'
        );
        DbProfiler::onSqlStatementExecuting($this->connection, $this->sql);
        DbProfiler::onSqlStatementExecuted();
        $this->assertTrue(file_exists(
            Config::getAppRootPath() . '/log/app.log'
        ));
    }

    public function testCustomLogger() {
        Config::set(
            'hyperframework.db.profiler.enable_logger', true
        );
        Config::set(
            'hyperframework.db.profiler.logger_class',
            'Hyperframework\Db\Test\CustomLogger'
        );
        DbProfiler::onSqlStatementExecuting($this->connection, $this->sql);
        DbProfiler::onSqlStatementExecuted();
        $this->assertNotNull(CustomLogger::getLog());
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidCustomLoggerClassConfig() {
        Config::set(
            'hyperframework.db.profiler.enable_logger', true
        );
        Config::set(
            'hyperframework.db.profiler.logger_class', 'Unknown'
        );
        DbProfiler::onSqlStatementExecuting($this->connection, $this->sql);
        DbProfiler::onSqlStatementExecuted();
    }

    private function mockProfileHandler() {
        $mock = $this->getMock('Hyperframework\Db\Test\ProfileHandler');
        DbProfiler::setProfileHandler($mock);
        return $mock->expects($this->once())->method('handle');
    }
}
