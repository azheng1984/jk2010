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

    public function testLogWarnning() {
        $time = time();
        Logger::warn(
            ['name' => 'Test', 'message' => 'hello',
                'time' => $time, 'data' => ['key' => 'value']]
        );
        $this->assertSame(
            date('Y-m-d H:i:s', $time) . ' | WARNING | Test | hello' . PHP_EOL
                . "\tkey: value" . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
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
        Logger::warn(['message' => ['xyz %d', 123]]);
        $this->assertSame(
            $time->format('Y-m-d H:i:s') . ' | WARNING || xyz 123' . PHP_EOL,
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

    public function testThreshold() {
        Config::set('hyperframework.logger.level', 'ERROR');
        Logger::warn(function() {
            return 'hello';
        });
        $this->assertFalse(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
        Config::set('hyperframework.logger.level', 'ERROR');
        Logger::error(function() {
            return 'hello';
        });
        $this->assertTrue(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
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
}
