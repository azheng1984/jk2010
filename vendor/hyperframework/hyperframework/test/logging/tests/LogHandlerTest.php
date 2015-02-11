<?php
namespace Hyperframework\Logging;

use Hyperframework\Test\TestCase as Base;
use Hyperframework\Common\Config;

class LogHandlerTest extends Base {
    protected function setUp() {
        Logger::setLevel(null);
        Logger::setLogHandler(null);
        Config::set('hyperframework.app_root_path', dirname(__DIR__));
    }

    protected function tearDown() {
        $path = Config::getAppRootPath() . '/log/app.log';
        if (file_exists($path)) {
            unlink($path);
        }
        if (file_exists(Config::getAppRootPath() . '/log/test/app.log')) {
            unlink(Config::getAppRootPath() . '/log/test/app.log');
        }
        if (file_exists(Config::getAppRootPath() . '/log/test')) {
            rmdir(Config::getAppRootPath() . '/log/test');
        }
        Config::clear();
    }

    public function testMessage() {
        $handler = new LogHandler;
        $time = time();
        $handler->handle(new LogRecord([
            'level' => 'ERROR',
            'message' => 'message',
            'time' => $time, 'name' => 'name'
        ]));
        $this->assertSame(
            date("Y-m-d H:i:s", $time) . ' [ERROR] message' . PHP_EOL,
            $this->getLogContent()
        );
    }

    private function getLogContent() {
        return file_get_contents(Config::getAppRootPath() . '/log/app.log');
    }

    public function testLogWithoutMessage() {
        $handler = new LogHandler;
        $time = time();
        $handler->handle(new LogRecord([
            'level' => 'ERROR', 'time' => $time, 'name' => 'name'
        ]));
        $this->assertSame(
            date("Y-m-d H:i:s", $time) . ' [ERROR]' . PHP_EOL,
            $this->getLogContent()
        );
    }

    public function testName() {
        $handler = new LogHandler;
        $time = time();
        $handler->handle(new LogRecord([
            'level' => 'ERROR', 'name' => 'name', 'time' => $time
        ]));
        $this->assertSame(
            date("Y-m-d H:i:s", $time) . ' [ERROR]' . PHP_EOL,
            $this->getLogContent()
        );
    }

    public function testChangeLogPath() {
        $handler = new LogHandler;
        Config::set('hyperframework.logging.log_path', 'log/test/app.log');
        $time = time();
        $handler->handle(new LogRecord([
            'level' => 'ERROR', 'time' => $time, 'name' => 'name'
        ]));
        $this->assertSame(
            date("Y-m-d H:i:s", $time) . ' [ERROR]' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/test/app.log')
        );
    }
}
