<?php
namespace Hyperframework\Logging;

use DateTime;
use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Logging\Test\CustomLogHandler;
use Hyperframework\Test\TestCase as Base;

class LoggerTest extends Base {
    protected function setUp() {
        Logger::setLevel(null);
        Logger::setLogHandler(null);
        Config::set( 'hyperframework.app_root_path', dirname(__DIR__));
    }

    protected function tearDown() {
        $path = Config::getAppRootPath() . '/log/app.log';
        if (file_exists($path)) {
            unlink($path);
        }
        Config::clear();
    }

    /**
     * @dataProvider getShortcutMethods
     */
    public function testShortcutMethods($method) {
        Logger::setLevel('DEBUG');
        if ($method === 'warn') {
            $level = 'WARNING';
        } else {
            $level = strtoupper($method);
        }
        $time = time();
        Logger::$method(
            ['name' => 'Test', 'message' => 'message',
                'time' => $time, 'key' => 'value']
        );
        $this->assertSame(
            date('Y-m-d H:i:s', $time) . ' | ' . $level . ' | Test | message'
                . PHP_EOL . "\tkey: value" . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function getShortcutMethods() {
        return [['debug'], ['info'], ['warn'],
            ['notice'], ['error'], ['fatal']];
    }

    public function testIntegerTimeForTimeOption() {
        $time = time();
        Logger::warn(
            ['name' => 'test', 'message' => 'message', 'time' => $time]
        );
        $this->assertSame(
            date('Y-m-d H:i:s', $time) . ' | WARNING | test | message' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testDateTimeForTimeOption() {
        $time = new DateTime;
        Logger::warn(['name' => 'test', 'message' => 'message', 'time' => $time]);
        $this->assertSame(
            $time->format('Y-m-d H:i:s') . ' | WARNING | test | message' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testMessageParams() {
        $time = new DateTime;
        Logger::warn(['name' => 'test', 'message' => ['%s', 'string']]);
        $this->assertSame(
            $time->format('Y-m-d H:i:s') . ' | WARNING | test | string' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testClosure() {
        $time = new DateTime;
        Logger::warn(function() {
            return ['name' => 'test', 'message' => 'message'];
        });
        $this->assertSame(
            $time->format('Y-m-d H:i:s') . ' | WARNING | test | message' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testSetLevel() {
        Logger::setLevel('ERROR');
        Logger::warn(function() {
            return ['name' => 'test', 'message' => 'message'];
        });
        $this->assertFalse(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
        Logger::error(function() {
            return ['name' => 'test', 'message' => 'message'];
        });
        $this->assertTrue(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testGetLevel() {
        $this->assertSame('INFO', Logger::getLevel());
    }

    public function testLowercaseLevel() {
        Logger::setLevel('debug');
        $this->assertSame('DEBUG', Logger::getLevel());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidLevel() {
        Logger::setLevel('unknown');
    }

    public function testSetCustomLogHandler() {
        $logHandler = new CustomLogHandler;
        Logger::setLogHandler($logHandler);
        $this->assertSame($logHandler, Logger::getLogHandler());
        $this->expectOutputString(get_class($logHandler) . '::handle');
        Logger::info(
            ['name' => 'test', 'message' => 'message']
        );
    }

    public function testDefaultLogHandler() {
        $this->assertTrue(Logger::getLogHandler() instanceof LogHandler); 
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvaidTime() {
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
    public function testInvaidLog() {
        try {
            Logger::warn(null);
        } catch (LoggingException $e) {
            $this->assertFalse(
                file_exists(Config::getAppRootPath() . '/log/app.log')
            );
            throw $e;
        }
    }

    public function testInvaidName() {
        try {
            Logger::warn(['name' => '.']);
            $this->fail();
        } catch (LoggingException $e) {
            $this->assertFalse(
                file_exists(Config::getAppRootPath() . '/log/app.log')
            );
        } catch (Exception $e) {
            $this->fail();
        }
        try {
            Logger::warn(['name' => '.name']);
            $this->fail();
        } catch (LoggingException $e) {
            $this->assertFalse(
                file_exists(Config::getAppRootPath() . '/log/app.log')
            );
        } catch (Exception $e) {
            $this->fail();
        }
        try {
            Logger::warn(['name' => 'name.']);
            $this->fail();
        } catch (LoggingException $e) {
            $this->assertFalse(
                file_exists(Config::getAppRootPath() . '/log/app.log')
            );
        } catch (Exception $e) {
            $this->fail();
        }
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvaidData() {
        try {
            Logger::warn(['data' => '.']);
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
    public function testInvaidDataKey() {
        try {
            Logger::warn(['.' => 'value']);
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
    public function testInvaidSecondLevelDataKey() {
        try {
            Logger::warn(['key' => ['.' => 'value']]);
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
    public function testInvalidLevelDefinedInConfig() {
        Config::set('hyperframework.logging.log_level', 'unknown');
        Logger::warn(['name' => 'test']);
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogHandlerClassDefinedInConfig() {
        Config::set('hyperframework.logging.log_handler_class', 'Unknown');
        Logger::warn(['name' => 'test']);
    }
}
