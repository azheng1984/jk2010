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
        //Config::set('hyperframework.cli.enable_subcommand', true);
        $commandConfig = $this->mockCommandConfig([
            'class' => 'Hyperframework\Cli\Test\Subcommands\ChildCommand'
        ], 'child');
        $argumentConfigs = $commandConfig->getArgumentConfigs('child');
        $config = $argumentConfigs[0];
        $this->assertSame('arg', $config->getName());
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidArgumentConfigs() {
        $commandConfig = $this->mockCommandConfig([
            'name' => 'test',
            'arguments' => false,
        ]);
        $this->assertSame([], $commandConfig->getArgumentConfigs());
    }

    public function testGetArgumentConfigsOfSubcommand() {
        Config::set('hyperframework.cli.enable_subcommand', true);
        $config = new CommandConfig;
        $argumentConfigs = $config->getArgumentConfigs('child');
        $config = $argumentConfigs[0];
        $this->assertSame('arg', $config->getName());
    }

    public function testGetClass() {
        $commandConfig = $this->mockCommandConfig([
            'class' => 'Class'
        ]);
        $this->assertSame('Class', $commandConfig->getClass());
    }

    public function testGetDefaultClass() {
        $commandConfig = $this->mockCommandConfig();
        $this->assertSame(
            'Hyperframework\Cli\Test\Command', $commandConfig->getClass()
        );
    }

    public function testGetSubcommandClass() {
        $commandConfig = $this->mockCommandConfig([
            'class' => 'Class'
        ], 'child');
        $this->assertSame('Class', $commandConfig->getClass('child'));
    }

    public function testGetSubcommandDefaultClass() {
        $commandConfig = $this->mockCommandConfig([], 'child');
        $this->assertSame(
            'Hyperframework\Cli\Test\Subcommands\ChildCommand',
            $commandConfig->getClass('child')
        );
    }

    public function testGetName() {
        $config = new CommandConfig;
        $this->assertSame('test', $config->getName());
    }

    public function mockCommandConfig(array $config = [], $subcommand = null) {
        $result = $this->getMockBuilder('Hyperframework\Cli\CommandConfig')
            ->setMethods(['getAll'])->getMock();
        $result->method('getAll')->will(
            $this->returnCallback(function($arg = null)
                use ($config, $subcommand) {
                    if ($arg === $subcommand) {
                        return $config;
                    } else {
                        $this->fail('Config is missing.');
                    }
                }
            )
        );
        return $result;
    }
}
