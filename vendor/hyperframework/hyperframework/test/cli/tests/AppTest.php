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
        $_SERVER['argv'] = ['run', '-t', 'arg'];
        $this->app = new App;
    }

    protected function tearDown() {
        parent::tearDown();
    }

    public function __construct() {
    }

    public function testInitializeOption() {
        $this->assertEquals($this->app->getOptions(), ['t' => true]);
    }

    public function testInitializeArgument() {
        $this->assertEquals($this->app->getArguments(), ['arg']);
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
        $this->app->run();
    }

    public function testRunx() {
    }
}
