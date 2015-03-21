<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;
use Hyperframework\Cli\Test\TestCase as Base;

class MultipleCommandAppTest extends Base {
    protected function setUp() {
        parent::setUp();
        Config::set(
            'hyperframework.cli.command_config_path', 'global_command.php'
        );
    }

    public function createApp($shouldCallConstructor = true) {
        $mock = $this->getMockBuilder('Hyperframework\Cli\MultipleCommandApp')
            ->setMethods([
                'quit',
                'initializeConfig',
                'initializeErrorHandler'
            ])
            ->disableOriginalConstructor()
            ->getMock();
        if ($shouldCallConstructor) {
            $mock->__construct(dirname(__DIR__));
        }
        return $mock;
    }

    public function testExecuteGlobalCommand() {
        $this->expectOutputString(
            "Usage: test [-t] [-h|--help] [--version] <command>" . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '-t'];
        $app = $this->createApp();
        $this->callProtectedMethod($app, 'executeCommand');
        $this->assertEquals($app->getGlobalOptions(), ['t' => true]);
    }

    public function testExecuteSubcommand() {
        $this->expectOutputString(
            'Hyperframework\Cli\Test\Subcommands\ChildCommand::execute'
        );
        $_SERVER['argv'] = ['run', 'child', '-c', 'arg'];
        $app = $this->createApp();
        $this->callProtectedMethod($app, 'executeCommand');
        $this->assertEquals($app->getOptions(), ['c' => true]);
        $this->assertEquals($app->getArguments(), ['arg']);
    }

    public function testConstruct() {
        $_SERVER['argv'] = ['run', '-t', 'child', '-c', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->getGlobalOptions(), ['t' => true]);
        $this->assertEquals($app->getOptions(), ['c' => true]);
        $this->assertEquals($app->getArguments(), ['arg']);
    }

    public function testHasGlobalOption() {
        $_SERVER['argv'] = ['run', '-t', 'child', '-c', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->hasGlobalOption('t'),  true);
        $this->assertEquals($app->hasGlobalOption('c'),  false);
    }

    public function testGetGlobalOption() {
        $_SERVER['argv'] = ['run', '-t', 'child', '-c', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->getGlobalOption('t'),  true);
        $this->assertEquals($app->hasGlobalOption('c'),  null);
    }

    public function testRenderHelp() {
        $this->expectOutputString(
            "Usage: test [-t] [-h|--help] [--version] <command>" . PHP_EOL
        );
        $_SERVER['argv'] = ['run', '-h'];
        $app = $this->createApp(false);
        $app->expects($this->once())->method('quit');
        $app->__construct(dirname(__DIR__));
    }

    public function testRenderSubcommandHelp() {
        $this->expectOutputString(
            "Usage: test child [-c] [-h|--help] <arg>" . PHP_EOL
        );
        $_SERVER['argv'] = ['run', 'child', '-h'];
        $app = $this->createApp(false);
        $app->expects($this->once())->method('quit');
        $app->__construct(dirname(__DIR__));
    }

    public function testRenderVersion() {
        $this->expectOutputString("1.0.0" . PHP_EOL);
        $_SERVER['argv'] = ['run', '--version'];
        $app = $this->createApp(false);
        $app->expects($this->once())->method('quit');
        $app->__construct(dirname(__DIR__));
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testSubcommandClassNotFound() {
        $_SERVER['argv'] = ['run', 'invalid-class-child'];
        $app = $this->createApp();
        $this->callProtectedMethod($app, 'executeCommand');
    }

    public function testGetSubcommand() {
        $_SERVER['argv'] = ['run', 'child', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->hasSubcommand(), true);
        $_SERVER['argv'] = ['run'];
        $app = $this->createApp();
        $this->assertEquals($app->hasSubcommand(), false);
    }

    public function testHasSubcommand() {
        $_SERVER['argv'] = ['run', 'child', 'arg'];
        $app = $this->createApp();
        $this->assertEquals($app->getSubcommand(), 'child');
        $_SERVER['argv'] = ['run'];
        $app = $this->createApp();
        $this->assertEquals($app->getSubcommand(), null);
    }
}
