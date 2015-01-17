<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;

class AppTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        Config::set(
            'hyperframework.app_root_path',
            '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli'
        );
        Config::set(
            'hyperframework.app_root_namespace', 'Hyperframework\Cli\Test'
        );
    }

    protected function tearDown() {
        Config::clear();
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

    public function testGetOption() {
        $_SERVER['argv'] = ['run', '-t', 'arg'];
        $app = new App;
        $this->assertEquals($app->getOption('t'), true);
    }

    public function testHasOption() {
        $_SERVER['argv'] = ['run', '-t', 'arg'];
        $app = new App;
        $this->assertEquals($app->hasOption('t'), true);
        $this->assertEquals($app->hasOption('x'), false);
    }

    public function testCustomHelp() {
        $this->expectOutputString("success");
        Config::set(
            'hyperframework.cli.help_class', 'Hyperframework\Cli\Test\Help'
        );
        $_SERVER['argv'] = ['run', '-h'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->__construct();
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testCustomHelpClassNotFound() {
        Config::set(
            'hyperframework.cli.help_class', 'Unknown'
        );
        $_SERVER['argv'] = ['run', '-h'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
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

    public function testRenderHelp() {
        $this->expectOutputString(
            "Usage: test [-t] [-h|--help] [--version] <arg>" . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '-h'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }

    public function testRenderVersion() {
        $this->expectOutputString("1.0.0" . PHP_EOL);
        $_SERVER['argv'] = ['run', '--version'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }

    public function testCustomCommandConfig() {
        Config::set(
            'hyperframework.cli.command_config_class',
            'Hyperframework\Cli\Test\CommandConfig'
        );
        $_SERVER['argv'] = ['run', 'arg'];
        $app = new App;
        $this->assertInstanceOf(
            'Hyperframework\Cli\Test\CommandConfig', $app->getCommandConfig()
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testCustomCommandConfigClassNotFound() {
        Config::set(
            'hyperframework.cli.command_config_class', 'Unknown'
        );
        $_SERVER['argv'] = ['run', 'arg'];
        $app = new App;
    }

    public function testVersionNotFound() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'command_version_not_found.php'
        );
        $this->expectOutputString("undefined" . PHP_EOL);
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
            "Unknown option 'unknown'."
                . PHP_EOL . "See 'test --help'." . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '--unknown'];
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('quit');
        $mock->__construct();
    }
}
