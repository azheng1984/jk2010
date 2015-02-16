<?php
namespace Hyperframework\Logging;

use Datetime;
use Hyperframework\Common\Config;
use Hyperframework\Logging\Test\CustomLogHandler;
use Hyperframework\Logging\Test\TestCase as Base;

class LoggerTest extends Base {
    private $handler;

    public static function setUpBeforeClass() {
        Logger::setLevel(null);
    }

    public function setUp() {
        $this->handler = $this->getMock('Hyperframework\Logging\LogHandler');
        Logger::setLogHandler($this->handler);
    }

    protected function tearDown() {
        Logger::setLevel(null);
        Logger::setLogHandler(null);
        parent::tearDown();
    }

    /**
     * @dataProvider getShortcutMethods
     */
    public function testShortcutMethods($method) {
        Logger::setLevel(LogLevel::DEBUG);
        if ($method === 'warn') {
            $level = 'WARNING';
        } else {
            $level = $method;
        }
        $this->handler->expects($this->once())->method('handle')->will(
            $this->returnCallback(function($logRecord) use ($level) {
                $this->assertSame(
                    LogLevel::getCode($level), $logRecord->getLevel()
                );
                $this->assertSame('message', $logRecord->getMessage());
            })
        )->with($this->isInstanceOf('Hyperframework\Logging\LogRecord'));
        Logger::$method('message');
    }

    public function getShortcutMethods() {
        return [
            ['debug'], ['info'], ['warn'], ['notice'], ['error'], ['fatal']
        ];
    }

    public function testLogByClosure() {
        $this->handler->expects($this->once())->method('handle')->will(
            $this->returnCallback(function($logRecord) {
                $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
                $this->assertSame('message', $logRecord->getMessage());
            })
        );
        Logger::log(LogLevel::ERROR, function() {
            return 'message';
        });
    }

    public function testLogByString() {
        $this->handler->expects($this->once())->method('handle')->will(
            $this->returnCallback(function($logRecord) {
                $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
                $this->assertSame('message', $logRecord->getMessage());
            })
        );
        Logger::log(LogLevel::ERROR, 'message');
    }

    public function testLogUsingEmptyArray() {
        $this->handler->expects($this->once())->method('handle');
        Logger::log(LogLevel::ERROR, []);
    }

    public function testCustomTime() {
        $time = new DateTime;
        $this->handler->expects($this->once())->method('handle')->will(
            $this->returnCallback(function($logRecord) use ($time) {
                $this->assertSame($time, $logRecord->getTime());
            })
        );
        Logger::log(LogLevel::ERROR, ['time' => $time]);
    }

    public function testDefaultLevel() {
        $this->handler->expects($this->once())->method('handle')->will(
            $this->returnCallback(function($logRecord) {
                $this->assertSame(LogLevel::INFO, $logRecord->getLevel());
            })
        );
        Logger::debug('message');
        Logger::info('message');
    }

    public function testChangeLevel() {
        $this->handler->expects($this->once())->method('handle')->will(
            $this->returnCallback(function($logRecord) {
                $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
            })
        );
        Logger::setLevel(LogLevel::ERROR);
        Logger::warn('message');
        Logger::error('message');
    }

    public function testChangeLevelUsingConfig() {
        $this->handler->expects($this->once())->method('handle')->will(
            $this->returnCallback(function($logRecord) {
                $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
            })
        );
        Config::set('hyperframework.logging.log_level', 'ERROR');
        Logger::warn('message');
        Logger::error('message');
    }

    public function testSetCustomLogHandlerUsingConfig() {
        Logger::setLogHandler(null);
        Config::set(
            'hyperframework.logging.log_handler_class',
            'Hyperframework\Logging\Test\CustomLogHandler'
        );
        $this->assertTrue(Logger::getLogHandler() instanceof CustomLogHandler); 
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvalidTime() {
        Logger::warn(['time' => 'invalid']);
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvalidLog() {
        Logger::error(null);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidLevelConfig() {
        Config::set('hyperframework.logging.log_level', 'UNKNOWN');
        Logger::error('message');
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogHandlerClassConfig() {
        Logger::setLogHandler(null);
        Config::set('hyperframework.logging.log_handler_class', 'Unknown');
        Logger::error('message');
    }

    private function hasAppLogFile() {
        return file_exists(Config::getAppRootPath() . '/log/app.log');
    }
}
