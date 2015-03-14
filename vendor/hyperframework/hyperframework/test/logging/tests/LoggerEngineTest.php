<?php
namespace Hyperframework\Logging;

use Datetime;
use Hyperframework\Common\Config;
use Hyperframework\Logging\Test\CustomLogHandler;
use Hyperframework\Logging\Test\TestCase as Base;

class LoggerEngineTest extends Base {
    private $loggerEngine;

    protected function setUp() {
        parent::setUp();
        $this->loggerEngine =
            $this->getMockBuilder('Hyperframework\Logging\LoggerEngine')
                ->setMethods(['getLogHandler'])->getMock();
    }

    public function testGenerateLogUsingClosure() {
        $this->mockLogHandler(function($logRecord) {
            $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
            $this->assertSame('message', $logRecord->getMessage());
        });
        $this->loggerEngine->log(LogLevel::ERROR, function() {
            return 'message';
        });
    }

    public function testLogString() {
        $this->mockLogHandler(function($logRecord) {
            $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
            $this->assertSame('message', $logRecord->getMessage());
        });
        $this->loggerEngine->log(LogLevel::ERROR, 'message');
    }

    public function testLogEmptyArray() {
        $this->mockLogHandler(function($logRecord) {
            $this->assertInstanceOf(
                'Hyperframework\Logging\LogRecord', $logRecord
            );
        });
        $this->loggerEngine->log(LogLevel::ERROR, []);
    }

    public function testLogCustomTime() {
        $time = new DateTime;
        $this->mockLogHandler(function($logRecord) use ($time) {
            $this->assertSame($time, $logRecord->getTime());
        });
        $this->loggerEngine->log(LogLevel::ERROR, ['time' => $time]);
    }

    public function testDefaultLevel() {
        $this->mockLogHandler(function($logRecord) {
            $this->assertSame(LogLevel::INFO, $logRecord->getLevel());
        });
        $this->loggerEngine->log(LogLevel::DEBUG, 'message');
        $this->loggerEngine->log(LogLevel::INFO, 'message');
    }

    public function testChangeLevel() {
        $this->mockLogHandler(function($logRecord) {
            $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
        });
        $this->loggerEngine->setLevel(LogLevel::ERROR);
        $this->loggerEngine->log(LogLevel::WARNING, 'message');
        $this->loggerEngine->log(LogLevel::ERROR, 'message');
    }

    public function testChangeLevelUsingConfig() {
        $this->mockLogHandler(function($logRecord) {
            $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
        });
        Config::set('hyperframework.logging.log_level', 'ERROR');
        $this->loggerEngine->log(LogLevel::WARNING, 'message');
        $this->loggerEngine->log(LogLevel::ERROR, 'message');
    }

    public function testSetCustomLogHandlerUsingConfig() {
        Config::set(
            'hyperframework.logging.log_handler_class',
            'Hyperframework\Logging\Test\CustomLogHandler'
        );
        $engine = new LoggerEngine;
        $this->assertInstanceOf(
            'Hyperframework\Logging\Test\CustomLogHandler',
            $this->callProtectedMethod($engine, 'getLogHandler')
        );
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvalidTime() {
        $this->loggerEngine->log(LogLevel::ERROR, ['time' => 'invalid']);
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvalidLog() {
        $this->loggerEngine->log(LogLevel::ERROR, null);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidLevelConfig() {
        Config::set('hyperframework.logging.log_level', 'UNKNOWN');
        $this->loggerEngine->log(LogLevel::ERROR, 'message');
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogHandlerClassConfig() {
        Config::set('hyperframework.logging.log_handler_class', 'Unknown');
        $engine = new LoggerEngine;
        $engine->log(LogLevel::ERROR, 'message');
    }

    private function mockLogHandler($handleCallback) {
        $logHandler = $this->getMock('Hyperframework\Logging\LogHandler');
        $logHandler->method('handle')->will($this->returnCallback(
            $handleCallback
        ));
        $this->loggerEngine->expects($this->once())
            ->method('getLogHandler')->willReturn($logHandler);
    }
}
