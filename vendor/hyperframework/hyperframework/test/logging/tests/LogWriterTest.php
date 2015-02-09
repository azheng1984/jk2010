<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class LogWriterTest extends Base {
    protected function setUp() {
        Config::set('hyperframework.app_root_path', dirname(__DIR__));
    }

    protected function tearDown() {
        $path = Config::getAppRootPath() . '/log/app.log';
        if (file_exists($path)) {
            unlink($path);
        }
        $path = Config::getAppRootPath() . '/log/test/app.log';
        if (file_exists($path)) {
            unlink($path);
            rmdir(dirname($path));
        }
        Config::clear();
        Logger::setLevel(null);
        Logger::setLogHandler(null);
    }

    public function testAppendLogFile() {
        $path = Config::getAppRootPath() . '/log/app.log';
        $writer = new LogWriter;
        $writer->write('record-1' . PHP_EOL);
        $writer->write('record-2' . PHP_EOL);
        return $this->assertSame(
            'record-1' . PHP_EOL . 'record-2' . PHP_EOL,
            file_get_contents($path)
        );
    }

    public function testCreateLogFolder() {
        Config::set('hyperframework.logging.log_path', 'log/test/app.log');
        $path = Config::getAppRootPath() . '/log/test/app.log';
        $writer = new LogWriter;
        $writer->write('content');
        return $this->assertSame('content', file_get_contents($path));
    }
}
