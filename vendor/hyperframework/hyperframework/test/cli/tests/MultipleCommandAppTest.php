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
            "Usage: test [-t] [-h|--help] [--version] <command>\n"
        );
        $_SERVER['argv'] = ['run', '-t'];
        $app = new MultipleCommandApp;
        $app->run();
        $this->assertEquals($app->getGlobalOptions(), ['t' => true]);
    }

    public function testRunSubcommand() {
        $_SERVER['argv'] = ['run', 'child', '-c', 'arg'];
        $app = new MultipleCommandApp;
        $app->run();
        $this->assertEquals($app->getOptions(), ['c' => true]);
        $this->assertEquals($app->getArguments(), ['arg']);
    }
}
