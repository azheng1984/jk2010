<?php
namespace Hyperframework\Logging;

use DateTime;
use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;
use PHPUnit_Framework_TestCase;

class LoggerTest extends PHPUnit_Framework_TestCase {
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
     * @dataProvider getLogMethods
     */
    public function testLog($method) {
        Logger::setLevel('DEBUG');
        if ($method === 'warn') {
            $level = 'WARNING';
        } else {
            $level = strtoupper($method);
        }
        $time = time();
        Logger::$method(
            ['name' => 'Test', 'message' => 'hello',
                'time' => $time, 'data' => ['key' => 'value']]
        );
        $this->assertSame(
            date('Y-m-d H:i:s', $time) . ' | ' . $level . ' | Test | hello'
                . PHP_EOL . "\tkey: value" . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function getLogMethods() {
        return [['debug'], ['info'], ['warn'],
            ['notice'], ['error'], ['fatal']];
    }

    public function testIntegerTime() {
        $time = time();
        Logger::warn(['message' => 'hello', 'time' => $time]);
        $this->assertSame(
            date('Y-m-d H:i:s', $time) . ' | WARNING || hello' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testDateTime() {
        $time = new DateTime;
        Logger::warn(['message' => 'hello', 'time' => $time]);
        $this->assertSame(
            $time->format('Y-m-d H:i:s') . ' | WARNING || hello' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testMessageParams() {
        $time = new DateTime;
        Logger::warn(['message' => ['%s', 'string']]);
        $this->assertSame(
            $time->format('Y-m-d H:i:s') . ' | WARNING || string' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testClosure() {
        $time = new DateTime;
        Logger::warn(function() {
            return 'hello';
        });
        $this->assertSame(
            $time->format('Y-m-d H:i:s') . ' | WARNING || hello' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testLevel() {
        Logger::setLevel('ERROR');
        Logger::warn(function() {
            return 'hello';
        });
        $this->assertFalse(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
        Logger::error(function() {
            return 'hello';
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

    public function testSetCustomLogHandler() {
        $logHandler = new LogHandler;
        Logger::setLogHandler($logHandler);
        $this->assertSame($logHandler, Logger::getLogHandler());
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
            Logger::warn(['data' => ['.' => 'value']]);
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
            Logger::warn(['data' => ['key' => ['.' => 'value']]]);
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
        Logger::warn('warning');
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogHandlerClassDefinedInConfig() {
        Config::set('hyperframework.logging.log_handler_class', 'Unknown');
        Logger::warn('warning');
    }
}
