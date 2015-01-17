<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;

/**
* @backupStaticAttributes enabled
*/
class RunnerTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        Config::set(
            'hyperframework.error_handler.class',
            'Hyperframework\Cli\Test\ErrorHandler'
        );
        Config::set(
            'hyperframework.app_root_path',
            '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli'
        );
        Config::set(
            'hyperframework.app_root_namespace', 'Hyperframework\Cli\Test'
        );
    }

    public function testRun() {
        $this->expectOutputString("Hyperframework\Cli\Test\Command::execute");
        $_SERVER['argv'] = ['run', 'arg'];
        Runner::run('/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
    }

    public function testInitializeAppRootPath() {
        $this->expectOutputString("Hyperframework\Cli\Test\Command::execute");
        $_SERVER['argv'] = ['run', 'arg'];
        Runner::run('/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
        $this->assertEquals(Config::get('hyperframework.app_root_path'), '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
    }

    public function testCustomApp() {
        $this->expectOutputString("Hyperframework\Cli\Test\App::run");
        Config::set('hyperframework.cli.app_class', 'Hyperframework\Cli\Test\App');
        $_SERVER['argv'] = ['run', 'arg'];
        Runner::run('/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testCustomAppClassNotFound() {
        Config::set('hyperframework.cli.app_class', 'Unknown');
        $_SERVER['argv'] = ['run', 'arg'];
        Runner::run('/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
    }
}
