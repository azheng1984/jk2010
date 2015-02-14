<?php
namespace Hyperframework\Logging\Test;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class TestCase extends Base {
    protected function setUp() {
        Config::set( 'hyperframework.app_root_path', dirname(__DIR__));
    }

    protected function deleteAppLogFile() {
        $path = Config::getAppRootPath() . '/log/app.log';
        if (file_exists($path)) {
            unlink($path);
        }
    }

    protected function getAppLogFileContent() {
        return file_get_contents(Config::getAppRootPath() . '/log/app.log');
    }
}
