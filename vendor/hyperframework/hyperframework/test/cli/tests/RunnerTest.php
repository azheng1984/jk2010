<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;

class RunnerTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        Config::set(
            'hyperframework.app_root_path',
            '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli'
        );
        Config::set(
            'hyperframework.app_root_namespace', 'Hyperframework\Cli\Test'
        );
    }

    public function testRun() {
        $this->expectOutputString("success");
        $_SERVER['argv'] = ['run', 'arg'];
        Runner::run('/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
    }
/*
    public function testInitializeAppRootPath() {
    }

    public function testCustomApp() {
    }

    public function testCustomAppClassNotFound() {
    }
*/
}
