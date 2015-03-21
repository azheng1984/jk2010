<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;
use Hyperframework\Cli\Test\TestCase as Base;

class CommandConfigTest extends Base {
    public function testGetGlobalCommandArgumentConfigs() {
        Config::set('hyperframework.cli.enable_subcommand', true);
        $config = new CommandConfig;
        $this->assertSame([], $config->getArgumentConfigs());
    }

    public function testGetDefaultArgumentConfigsOfSubcommand() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'invalid_command_arguments.php'
        );
        $config = new CommandConfig;
        $argumentConfigs = $config->getArgumentConfigs(
            'default-argument-config-child'
        );
        $config = $argumentConfigs[0];
        $this->assertSame('arg', $config->getName());
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidArgumentConfigs() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'invalid_command_arguments.php'
        );
        $config = new CommandConfig;
        $this->assertSame([], $config->getArgumentConfigs());
    }

    public function testGetArgumentConfigsOfSubcommand() {
        $config = new CommandConfig;
        $argumentConfigs = $config->getArgumentConfigs('child');
        $config = $argumentConfigs[0];
        $this->assertSame('arg', $config->getName());
    }

    public function testGetName() {
        $config = new CommandConfig;
        $this->assertSame('test', $config->getName());
    }
}
