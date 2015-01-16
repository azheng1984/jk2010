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
        $_SERVER['argv'] = ['run'];
        $app = new MultipleCommandApp;
        $app->run();
    }

    public function testRunSubcommand() {
        $_SERVER['argv'] = ['run', 'subcommand'];
        $app = new MultipleCommandApp;
        $app->run();
    }
}
