<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Logging\Test\CustomLogHandler;
use Hyperframework\Logging\Test\TestCase as Base;

class LoggerTest extends Base {
    protected function tearDown() {
        $this->deleteAppLogFile();
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
            $level = strtoupper($method);
        }
        Logger::$method('message');
        $this->assertTrue($this->hasAppLogFile());
    }

    public function getShortcutMethods() {
        return [
            ['debug'], ['info'], ['warn'], ['notice'], ['error'], ['fatal']
        ];
    }

    public function testLogByClosure() {
        Logger::log(LogLevel::ERROR, function() {
            return 'message';
        });
        $this->assertTrue($this->hasAppLogFile());
    }

    public function testLogByString() {
        Logger::log(LogLevel::ERROR, 'message');
        $this->assertTrue($this->hasAppLogFile());
    }

    public function testLogUsingEmptyArray() {
        Logger::log(LogLevel::ERROR, []);
        $this->assertTrue($this->hasAppLogFile());
    }

    public function testDefaultLevel() {
        Logger::debug('message');
        $this->assertFalse($this->hasAppLogFile());
        Logger::info('message');
        $this->assertTrue($this->hasAppLogFile());
    }

    public function testChangeLevel() {
        Logger::setLevel(LogLevel::ERROR);
        Logger::warn('message');
        $this->assertFalse($this->hasAppLogFile());
        Logger::error('message');
        $this->assertTrue($this->hasAppLogFile());
    }

    public function testChangeLevelUsingConfig() {
        Config::set('hyperframework.logging.log_level', 'ERROR');
        Logger::warn('message');
        $this->assertFalse($this->hasAppLogFile());
        Logger::error('message');
        $this->assertTrue($this->hasAppLogFile());
    }

    public function testCustomLogHandler() {
        $logHandler = $this->getMock('Hyperframework\Logging\LogHandler');
        $logHandler->expects($this->once())->method('handle')
            ->with($this->isInstanceOf('Hyperframework\Logging\LogRecord'));
        Logger::setLogHandler($logHandler);
        Logger::error('message');
    }

    public function testSetCustomLogHandlerUsingConfig() {
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
        Config::set('hyperframework.logging.log_handler_class', 'Unknown');
        Logger::error('message');
    }

    private function hasAppLogFile() {
        return file_exists(Config::getAppRootPath() . '/log/app.log');
    }
}
