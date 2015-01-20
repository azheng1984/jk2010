<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class LoggerTest extends Base {
    protected function tearDown() {
        unlink(Config::getAppRootPath() . '/log/app.log');
    }

    public function testLogWarnning() {
        Config::set( 'hyperframework.app_root_path', dirname(__DIR__));
        Logger::warn('hello');
        $this->assertStringEndsWith(
            ' | WARNING || hello' . PHP_EOL,
            file_get_contents(Config::getAppRootPath() . '/log/app.log')
        );
    }
}
