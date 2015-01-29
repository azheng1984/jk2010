<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class AppTest extends Base {
    protected function setUp() {
        Config::set(
            'hyperframework.app_root_path', dirname(__DIR__)
        );
        Config::set(
            'hyperframework.app_root_namespace', 'Hyperframework\Cli\Test'
        );
    }

    protected function tearDown() {
        Config::clear();
    }

    public function createApp() {
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit', 'initializeConfig', 'initializeErrorHandler', 'initializeAppRootPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->__construct(null);
        return $mock;
    }

    public function testInitializeOption() {
        $_SERVER['argv'] = ['run', '-t', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->getOptions(), ['t' => true]);
    }

    public function testInitializeArgument() {
        $_SERVER['argv'] = ['run', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->getArguments(), ['arg']);
    }

    public function testGetOption() {
        $_SERVER['argv'] = ['run', '-t', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->getOption('t'), true);
    }

    public function testHasOption() {
        $_SERVER['argv'] = ['run', '-t', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->hasOption('t'), true);
        $this->assertEquals($app->hasOption('x'), false);
    }

    public function testCustomHelp() {
        $this->expectOutputString("Hyperframework\Cli\Test\Help::render");
        Config::set(
            'hyperframework.cli.help_class', 'Hyperframework\Cli\Test\Help'
        );
        $_SERVER['argv'] = ['run', '-h'];
        $app = $this->createApp();
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testCustomHelpClassNotFound() {
        Config::set(
            'hyperframework.cli.help_class', 'Unknown'
        );
        $_SERVER['argv'] = ['run', '-h'];
        $app = $this->createApp();
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testCommandClassNotFound() {
        Config::set(
            'hyperframework.cli.command_config_path', 'command_class_error.php'
        );
        $_SERVER['argv'] = ['run'];
        $app = $this->createApp();
        $app->run(null);
    }

    public function testRenderHelp() {
        $this->expectOutputString(
            "Usage: test [-t] [-h|--help] [--version] <arg>" . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '-h'];
        $this->createApp();
    }

    public function testRenderVersion() {
        $this->expectOutputString("1.0.0" . PHP_EOL);
        $_SERVER['argv'] = ['run', '--version'];
        $this->createApp();
    }

    public function testCustomCommandConfig() {
        Config::set(
            'hyperframework.cli.command_config_class',
            'Hyperframework\Cli\Test\CommandConfig'
        );
        $_SERVER['argv'] = ['run', 'arg'];
        $app = $this->createApp();
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
        $app = $this->createApp();
    }

    public function testVersionNotFound() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'command_version_not_found.php'
        );
        $this->expectOutputString("undefined" . PHP_EOL);
        $_SERVER['argv'] = ['run', '--version'];
        $app = $this->createApp();
    }

    public function testRun() {
        $this->expectOutputString('Hyperframework\Cli\Test\Command::execute');
        $_SERVER['argv'] = ['run', 'arg'];
        $app = $this->createApp();
        $app->run(null);
    }

    public function testCommandParsingError() {
        $this->expectOutputString(
            "Unknown option 'unknown'."
                . PHP_EOL . "See 'test --help'." . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '--unknown'];
        $app = $this->createApp();
    }
}
