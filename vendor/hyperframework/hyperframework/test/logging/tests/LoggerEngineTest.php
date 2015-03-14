<?php
namespace Hyperframework\Logging;

use Datetime;
use Hyperframework\Common\Config;
use Hyperframework\Logging\Test\CustomLogHandler;
use Hyperframework\Logging\Test\TestCase as Base;

class LoggerEngineTest extends Base {
    /**
     * @dataProvider getShortcutMethods
     */
//    public function testShortcutMethods($method) {
//        Logger::setLevel(LogLevel::DEBUG);
//        if ($method === 'warn') {
//            $level = 'WARNING';
//        } else {
//            $level = $method;
//        }
//        $this->setHandleMethod(function($logRecord) use ($level) {
//            $this->assertSame(
//                LogLevel::getCode($level), $logRecord->getLevel()
//            );
//            $this->assertSame('message', $logRecord->getMessage());
//        });
//        Logger::$method('message');
//    }
//
//    public function getShortcutMethods() {
//        return [
//            ['debug'], ['info'], ['warn'], ['notice'], ['error'], ['fatal']
//        ];
//    }
//
    public function testGenerateLogUsingClosure() {
        $this->setHandleMethod(function($logRecord) {
            $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
            $this->assertSame('message', $logRecord->getMessage());
        });
        Logger::log(LogLevel::ERROR, function() {
            return 'message';
        });
    }
//
//    public function testLogString() {
//        $this->setHandleMethod(function($logRecord) {
//            $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
//            $this->assertSame('message', $logRecord->getMessage());
//        });
//        Logger::log(LogLevel::ERROR, 'message');
//    }
//
//    public function testLogEmptyArray() {
//        $this->mockHandler();
//        Logger::getLogHandler()->expects($this->once())->method('handle')
//            ->with($this->isInstanceOf('Hyperframework\Logging\LogRecord'));
//        Logger::log(LogLevel::ERROR, []);
//    }
//
//    public function testLogCustomTime() {
//        $time = new DateTime;
//        $this->setHandleMethod(function($logRecord) use ($time) {
//            $this->assertSame($time, $logRecord->getTime());
//        });
//        Logger::log(LogLevel::ERROR, ['time' => $time]);
//    }
//
//    public function testDefaultLevel() {
//        $this->setHandleMethod(function($logRecord) {
//            $this->assertSame(LogLevel::INFO, $logRecord->getLevel());
//        });
//        Logger::debug('message');
//        Logger::info('message');
//    }
//
//    public function testChangeLevel() {
//        $this->setHandleMethod(function($logRecord) {
//            $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
//        });
//        Logger::setLevel(LogLevel::ERROR);
//        Logger::warn('message');
//        Logger::error('message');
//    }
//
//    public function testChangeLevelUsingConfig() {
//        $this->setHandleMethod(function($logRecord) {
//            $this->assertSame(LogLevel::ERROR, $logRecord->getLevel());
//        });
//        Config::set('hyperframework.logging.log_level', 'ERROR');
//        Logger::warn('message');
//        Logger::error('message');
//    }
//
//    public function testSetCustomLogHandlerUsingConfig() {
//        Config::set(
//            'hyperframework.logging.log_handler_class',
//            'Hyperframework\Logging\Test\CustomLogHandler'
//        );
//        $this->assertTrue(Logger::getLogHandler() instanceof CustomLogHandler); 
//    }
//
//    /**
//     * @expectedException Hyperframework\Logging\LoggingException
//     */
//    public function testInvalidTime() {
//        Logger::warn(['time' => 'invalid']);
//    }
//
//    /**
//     * @expectedException Hyperframework\Logging\LoggingException
//     */
//    public function testInvalidLog() {
//        Logger::error(null);
//    }
//
//    /**
//     * @expectedException Hyperframework\Common\ConfigException
//     */
//    public function testInvalidLevelConfig() {
//        Config::set('hyperframework.logging.log_level', 'UNKNOWN');
//        Logger::error('message');
//    }
//
//    /**
//     * @expectedException Hyperframework\Common\ClassNotFoundException
//     */
//    public function testInvalidLogHandlerClassConfig() {
//        Config::set('hyperframework.logging.log_handler_class', 'Unknown');
//        Logger::error('message');
//    }
//
    private function setHandleMethod($callback) {
        $this->mockHandler();
        Logger::getLogHandler()->expects($this->once())->method('handle')->will(
            $this->returnCallback($callback)
        );
    }
//
//    private function mockHandler() {
//        $mock = $this->getMock('Hyperframework\Logging\LogHandler');
//        Logger::setLogHandler($mock);
//    }
}
