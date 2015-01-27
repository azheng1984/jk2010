<?php
namespace Hyperframework\Logging;

use Hyperframework\Test\TestCase as Base;
use Hyperframework\Common\Config;

class LogHandlerTest extends Base {
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

    public function testHandle() {
        $handler = new LogHandler;
        $handler->handle('ERROR', ['message' => 'message']);
        $this->assertTrue(
            file_exists(Config::getAppRootPath() . '/log/app.log')
        );
    }
}
