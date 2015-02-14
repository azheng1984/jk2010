<?php
namespace Hyperframework\Logging;

use DateTime;
use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Logging\Test\CustomLogHandler;
use Hyperframework\Test\TestCase as Base;

class LoggerTest extends Base {
    protected function setUp() {
        Config::set( 'hyperframework.app_root_path', dirname(__DIR__));
    }

    protected function tearDown() {
        $path = Config::getAppRootPath() . '/log/app.log';
        if (file_exists($path)) {
            unlink($path);
        }
        Config::clear();
        Logger::setLevel(null);
        Logger::setLogHandler(null);
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
        $time = time();
        Logger::$method(
            ['message' => 'message', 'time' => $time]
        );
        $this->assertSame(
            date('Y-m-d H:i:s', $time) . ' [' . $level . '] message' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function getShortcutMethods() {
        return [
            ['debug'], ['info'], ['warn'], ['notice'], ['error'], ['fatal']
        ];
    }

    public function testLogByClosure() {
        $time = new DateTime;
        Logger::log(LogLevel::ERROR, function() use ($time) {
            return ['message' => 'message', 'time' => $time];
        });
        $this->assertSame(
            $time->format('Y-m-d H:i:s') . ' [ERROR] message' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testLogByString() {
        Logger::log(LogLevel::ERROR, 'message');
        $this->assertStringEndsWith(
            ' [ERROR] message' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testLogUsingEmptyArray() {
        Logger::log(LogLevel::ERROR, []);
        $this->assertStringEndsWith(
            ' [ERROR]' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testDefaultLevel() {
        Logger::debug(function() {
            return 'message';
        });
        $this->assertFalse(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
        Logger::info(function() {
            return 'message';
        });
        $this->assertTrue(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testChangeLevel() {
        Logger::setLevel(LogLevel::ERROR);
        Logger::warn(function() {
            return 'message';
        });
        $this->assertFalse(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
        Logger::error(function() {
            return 'message';
        });
        $this->assertTrue(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testChangeLevelUsingConfig() {
        Config::set('hyperframework.logging.log_level', 'ERROR');
        Logger::warn(function() {
            return 'message';
        });
        $this->assertFalse(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
        Logger::error(function() {
            return 'message';
        });
        $this->assertTrue(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testCustomLogHandler() {
        $logHandler = $this->getMockBuilder(
            'Hyperframework\Logging\LogHandler'
        )->getMock();
        $logHandler->expects($this->once())->method('handle')
            ->with($this->isInstanceOf('Hyperframework\Logging\LogRecord'));
        Logger::setLogHandler($logHandler);
        Logger::error('');
    }

    public function testSetCustomLogHandlerUsingConfig() {
        Config::set(
            'hyperframework.logging.log_handler_class',
            'Hyperframework\Logging\Test\CustomLogHandler'
        );
        $this->assertTrue(Logger::getLogHandler() instanceof CustomLogHandler); 
    }

    public function testDefaultLogHandler() {
        $this->assertTrue(Logger::getLogHandler() instanceof LogHandler); 
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvalidTime() {
        try {
            Logger::warn(['time' => 'invalid']);
        } catch (LoggingException $e) {
            $this->assertFalse(
                file_exists(Config::getAppRootPath() . '/log/app.log')
            );
            throw $e;
        }
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvalidLog() {
        try {
            Logger::error(null);
        } catch (LoggingException $e) {
            $this->assertFalse(
                file_exists(Config::getAppRootPath() . '/log/app.log')
            );
            throw $e;
        }
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidLevelConfig() {
        Config::set('hyperframework.logging.log_level', 'UNKNOWN');
        Logger::error('');
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogHandlerClassConfig() {
        Config::set('hyperframework.logging.log_handler_class', 'Unknown');
        Logger::error('');
    }
}
