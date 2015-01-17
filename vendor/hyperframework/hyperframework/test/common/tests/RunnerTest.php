<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Config;
use Hyperframework\Common\Test\InitializeAppRootPathMethodNotImplementedRunner;

class RunnerTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        Config::set(
            'hyperframework.error_handler.class',
            'Hyperframework\Cli\Test\ErrorHandler'
        );
    }

    /**
     * @expectedException Hyperframework\Common\NotImplementedException
     */
    public function testInitializeAppRootMethodNotImplemented() {
        InitializeAppRootPathMethodNotImplementedRunner::run(
            '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli'
        );
        $this->assertEquals(Config::get('hyperframework.app_root_path'), dirname(getcwd()));
    }

    public function testInitializeAppRootPath() {
        $this->expectOutputString("Hyperframework\Cli\Test\Command::execute");
        $_SERVER['argv'] = ['run', 'arg'];
        Runner::run('/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
        $this->assertEquals(Config::get('hyperframework.app_root_path'), '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
    }
}
