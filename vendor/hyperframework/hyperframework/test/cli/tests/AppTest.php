<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\App;
use Hyperframework\Cli\Test\GlobalApp;
use Hyperframework\Common\Config;
use Hyperframework\Cli\Test\TestCase as Base;

class AppTest extends Base {
    public function createApp() {
        $mock = $this->getMockBuilder('Hyperframework\Cli\App')
            ->setMethods(['quit', 'initializeConfig', 'initializeErrorHandler'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->__construct(dirname(__DIR__));
        return $mock;
    }

    public function testRun() {
        $app = $this->getMockBuilder('Hyperframework\Cli\Test\App')
            ->setConstructorArgs([dirname(__DIR__)])
            ->setMethods(['executeCommand', 'finalize'])->getMock();
        $app->expects($this->once())->method('executeCommand');
        $app->expects($this->once())->method('finalize');
        $GLOBALS['app'] = $app;
        GlobalApp::run('');
    }

    public function testCreateApp() {
        $this->assertInstanceOf(
            'Hyperframework\Cli\Test\App',
            $this->callProtectedMethod(
                'Hyperframework\Cli\Test\App', 'createApp', ['']
            )
        );
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
        //echo Config::get('hyperframework.app_root_path');
        $app->run(dirname(__dir__));
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

    public function testCommandParsingError() {
        $this->expectOutputString(
            "Unknown option 'unknown'."
                . PHP_EOL . "See 'test --help'." . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '--unknown'];
        $app = $this->createApp();
    }
}
