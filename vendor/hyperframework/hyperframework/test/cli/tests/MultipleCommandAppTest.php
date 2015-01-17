<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;

class MultipleCommandAppTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        Config::set(
            'hyperframework.app_root_path',
            '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli'
        );
        Config::set(
            'hyperframework.app_root_namespace', 'Hyperframework\Cli\Test'
        );
        Config::set(
            'hyperframework.cli.command_config_path', 'global_command.php'
        );
    }

    public function testRunGlobalCommand() {
        $this->expectOutputString(
            "Usage: test [-t] [-h|--help] [--version] <command>" . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '-t'];
        $app = new MultipleCommandApp;
        $app->run();
        $this->assertEquals($app->getGlobalOptions(), ['t' => true]);
    }

    protected function tearDown() {
        Config::clear();
    }

    public function testInitialize() {
        $_SERVER['argv'] = ['run', '-t', 'child', '-c', 'arg'];
        $app = new MultipleCommandApp;
        $app->run();
        $this->assertEquals($app->getGlobalOptions(), ['t' => true]);
        $this->assertEquals($app->getOptions(), ['c' => true]);
        $this->assertEquals($app->getArguments(), ['arg']);
    }

    public function testHasGlobalOption() {
        $_SERVER['argv'] = ['run', '-t', 'child', '-c', 'arg'];
        $app = new MultipleCommandApp;
        $app->run();
        $this->assertEquals($app->hasGlobalOption('t'),  true);
        $this->assertEquals($app->hasGlobalOption('c'),  false);
    }

    public function testGetGlobalOption() {
        $_SERVER['argv'] = ['run', '-t', 'child', '-c', 'arg'];
        $app = new MultipleCommandApp;
        $app->run();
        $this->assertEquals($app->getGlobalOption('t'),  true);
        $this->assertEquals($app->hasGlobalOption('c'),  null);
    }

    public function testRunSubcommand() {
        $_SERVER['argv'] = ['run', 'child', '-c', 'arg'];
        $app = new MultipleCommandApp;
        $app->run();
        $this->assertEquals($app->getOptions(), ['c' => true]);
        $this->assertEquals($app->getArguments(), ['arg']);
    }

    public function testRenderHelp() {
        $this->expectOutputString(
            "Usage: test [-t] [-h|--help] [--version] <command>" . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '-h'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\MultipleCommandApp')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }

    public function testRenderSubcommandHelp() {
        $this->expectOutputString(
            "Usage: test child [-c] [-h|--help] <arg>" . PHP_EOL
        );
        $_SERVER['argv'] = ['run', 'child', '-h'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\MultipleCommandApp')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }

    public function testRenderVersion() {
        $this->expectOutputString("1.0.0" . PHP_EOL);
        $_SERVER['argv'] = ['run', '--version'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\MultipleCommandApp')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testSubcommandClassNotFound() {
        $_SERVER['argv'] = ['run', 'child_class_error'];
        $app = new MultipleCommandApp;
        $app->run();
    }

    public function testGetSubcommand() {
        $_SERVER['argv'] = ['run', 'child', 'arg'];
        $app = new MultipleCommandApp;
        $this->assertEquals($app->hasSubcommand(), true);
        $_SERVER['argv'] = ['run'];
        $app = new MultipleCommandApp;
        $this->assertEquals($app->hasSubcommand(), false);
    }

    public function testHasSubcommand() {
        $_SERVER['argv'] = ['run', 'child', 'arg'];
        $app = new MultipleCommandApp;
        $this->assertEquals($app->getSubcommand(), 'child');
        $_SERVER['argv'] = ['run'];
        $app = new MultipleCommandApp;
        $this->assertEquals($app->getSubcommand(), null);
    }
}
