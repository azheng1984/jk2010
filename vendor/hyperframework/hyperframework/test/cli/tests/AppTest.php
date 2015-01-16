<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;

class AppTest extends \PHPUnit_Framework_TestCase {
    private $app;

    protected function setUp() {
        parent::setUp();
        Config::set(
            'hyperframework.app_root_path',
            '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli'
        );
        Config::set(
            'hyperframework.app_root_namespace', 'Hyperframework\Cli\Test'
        );
    }

    protected function tearDown() {
        Config::set('hyperframework.cli.command_config_path', null);
    }

    public function testInitializeOption() {
        $_SERVER['argv'] = ['run', '-t', 'arg'];
        $app = new App;
        $this->assertEquals($app->getOptions(), ['t' => true]);
    }

    public function testInitializeArgument() {
        $_SERVER['argv'] = ['run', 'arg'];
        $app = new App;
        $this->assertEquals($app->getArguments(), ['arg']);
    }

    public function testShowHelp() {
        $this->expectOutputString(
            "Usage: test [-t] [-h|--help] [--version] <arg>\n"
        );
        $_SERVER['argv'] = ['run', '-h'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testCommandClassNotFound() {
        Config::set(
            'hyperframework.cli.command_config_path', 'command_class_error.php'
        );
        $_SERVER['argv'] = ['run'];
        $app = new App;
        $app->run();
    }

    public function testShowVersion() {
        $this->expectOutputString("1.0.0\n");
        $_SERVER['argv'] = ['run', '--version'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }

    public function testRun() {
        $this->expectOutputString('success');
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['finalize'])->getMock();
        $mock->expects($this->once())->method('finalize');
        $mock->run();
    }

    public function testCommandParsingError() {
        $this->expectOutputString(
            "Unknown option 'unkonwn'.\nSee 'test --help'.\n"
        );
        $_SERVER['argv'] = ['run', '--unkonwn'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }
}
