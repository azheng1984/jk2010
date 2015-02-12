<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Logging\Test\CustomLogFormatter;
use Hyperframework\Logging\Test\CustomLogWriter;
use Hyperframework\Test\TestCase as Base;

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

    public function testHandleLog() {
        $time = time();
        $handler = new LogHandler;
        $handler->handle(new LogRecord(
            ['time' => $time, 'level' => 'ERROR']
        ));
        $this->assertSame(
            date("Y-m-d H:i:s", $time) . ' [ERROR]' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }

    public function testDefaultLogWriter() {
        $handler = new LogHandler;
        $this->assertTrue(
            $this->callProtectedMethod($handler, 'getWriter')
                instanceof LogWriter
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogWriter() {
        Config::set('hyperframework.logging.log_writer_class', 'Unknown');
        $handler = new LogHandler;
        $this->callProtectedMethod($handler, 'getWriter');
    }

    public function testCustomLogWriter() {
        Config::set(
            'hyperframework.logging.log_writer_class',
            'Hyperframework\Logging\Test\CustomLogWriter'
        );
        $handler = new LogHandler;
        $this->assertTrue(
            $this->callProtectedMethod($handler, 'getWriter')
                instanceof CustomLogWriter
        );
    }

    public function testDefaultLogFormatter() {
        $handler = new LogHandler;
        $this->assertTrue(
            $this->callProtectedMethod($handler, 'getFormatter')
                instanceof LogFormatter
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogFormatter() {
        Config::set('hyperframework.logging.log_formatter_class', 'Unknown');
        $handler = new LogHandler;
        $this->callProtectedMethod($handler, 'getFormatter');
    }

    public function testCustomLogFormatter() {
        Config::set(
            'hyperframework.logging.log_formatter_class',
            'Hyperframework\Logging\Test\CustomLogFormatter'
        );
        $handler = new LogHandler;
        $this->assertTrue(
            $this->callProtectedMethod($handler, 'getFormatter')
                instanceof CustomLogFormatter
        );
    }
}
