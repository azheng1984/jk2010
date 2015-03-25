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
        $commandConfig = $this->mockCommandConfig([
            'class' => 'Hyperframework\Cli\Test\Subcommands\ChildCommand'
        ], 'child');
        $argumentConfigs = $commandConfig->getArgumentConfigs('child');
        $config = $argumentConfigs[0];
        $this->assertSame('arg', $config->getName());
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testGetDefaultArgumentWhenCommandClassNotFound() {
        $commandConfig = $this->mockCommandConfig([
            'class' => 'Unknown'
        ]);
        $argumentConfigs = $commandConfig->getArgumentConfigs();
    }

    /**
     * @expectedException Hyperframework\Common\MethodNotFoundException
     */
    public function testGetDefaultArgumentWhenExecuteMethodNotFound() {
        $commandConfig = $this->mockCommandConfig([
            'class' =>
                'Hyperframework\Cli\Test\Subcommands\NoExecuteMethodCommand'
        ]);
        $argumentConfigs = $commandConfig->getArgumentConfigs();
    }

    /**
     * @expectedException LogicException
     */
    public function testGetDefaultArgumentWhenArrayArgumentIsInvalid() {
        $commandConfig = $this->mockCommandConfig([
            'class' =>
            'Hyperframework\Cli\Test\Subcommands\\'
                . 'InvalidArrayArgumentCommand'
        ]);
        $argumentConfigs = $commandConfig->getArgumentConfigs();
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

    public function testGetDefaultSubcommandClass() {
        $commandConfig = $this->mockCommandConfig([], 'test-child');
        $this->assertSame(
            'Hyperframework\Cli\Test\Subcommands\TestChildCommand',
            $commandConfig->getClass('test-child')
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

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testGetInvalidClass() {
        $commandConfig = $this->mockCommandConfig([
            'class' => false
        ]);
        $commandConfig->getClass();
    }

    public function testGetOptionConfigIndex() {
        $commandConfig = $this->mockCommandConfig([
            'options' => [['name' => 'test']]
        ]);
        $configs = $commandConfig->getOptionConfigIndex();
        $this->assertTrue(isset($configs['test']));
    }

    public function testDefaultHelpAndVersionOptionConfigs() {
        $commandConfig = $this->mockCommandConfig();
        $configs = $commandConfig->getOptionConfigIndex();
        $this->assertTrue(isset($configs['help']));
        $this->assertFalse(isset($configs['version']));
        $commandConfig = $this->mockCommandConfig(['version' => 1]);
        $configs = $commandConfig->getOptionConfigIndex();
        $this->assertTrue(isset($configs['version']));
    }

    public function testGetOptionConfigIndexOfSubcommand() {
        $commandConfig = $this->mockCommandConfig([
            'options' => [['name' => 'test']]
        ], 'child');
        $configs = $commandConfig->getOptionConfigIndex('child');
        $this->assertTrue(isset($configs['test']));
    }

    public function testDefaultHelpOptionConfigOfSubcommand() {
        $commandConfig = $this->mockCommandConfig([], 'child');
        $configs = $commandConfig->getOptionConfigIndex('child');
        $this->assertTrue(isset($configs['help']));
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testGetInvalidOptionConfigs() {
        $commandConfig = $this->mockCommandConfig([
            'options' => false
        ]);
        $commandConfig->getOptionConfigs();
    }

    public function testGetMutuallyExclusiveOptionGroupConfigs() {
        $commandConfig = $this->mockCommandConfig([
            'options' => [['name' => 'test'], ['name' => 'test2']],
            'mutually_exclusive_option_groups' => [['test', 'test2']]
        ]);
        $configs = $commandConfig->getMutuallyExclusiveOptionGroupConfigs();
        $config = $configs[0];
        $this->assertSame(2, count($config->getOptionConfigs()));
    }

    public function testGetMutuallyExclusiveOptionGroupConfigsOfSubcommand() {
        $commandConfig = $this->mockCommandConfig([
            'options' => [['name' => 'test'], ['name' => 'test2']],
            'mutually_exclusive_option_groups' => [['test', 'test2']]
        ], 'child');
        $configs = $commandConfig->getMutuallyExclusiveOptionGroupConfigs(
            'child'
        );
        $config = $configs[0];
        $this->assertSame(2, count($config->getOptionConfigs()));
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testGetInvalidMutuallyExclusiveOptionGroupConfigs() {
        $commandConfig = $this->mockCommandConfig([
            'mutually_exclusive_option_groups' => false
        ]);
        $commandConfig->getMutuallyExclusiveOptionGroupConfigs();
    }

    public function testGetDescription() {
        $commandConfig = $this->mockCommandConfig([
            'description' => 'test'
        ]);
        $this->assertSame('test', $commandConfig->getDescription());
    }

    public function testGetName() {
        $commandConfig = $this->mockCommandConfig([
            'name' => 'test'
        ]);
        $this->assertSame('test', $commandConfig->getName());
    }

    public function testGetVersion() {
        $commandConfig = $this->mockCommandConfig([
            'version' => '1.0.0'
        ]);
        $this->assertSame('1.0.0', $commandConfig->getVersion());
    }

    public function testIsSubcommandEnabled() {
        $commandConfig = new CommandConfig;
        Config::set('hyperframework.cli.enable_subcommand', true);
        $this->assertTrue($commandConfig->isSubcommandEnabled());
        Config::set('hyperframework.cli.enable_subcommand', false);
        $this->assertTrue($commandConfig->isSubcommandEnabled());
    }

    public function testHasSubcommand() {
        $commandConfig = $this->getMockBuilder('Hyperframework\Cli\CommandConfig')
            ->setMethods(['getSubcommandNames'])->getMock();
        $commandConfig->method('getSubcommandNames')->willReturn(['test']);
        $this->assertTrue($commandConfig->hasSubcommand('test'));
    }

    public function testGetSubcommandNames() {
        Config::set('hyperframework.cli.enable_subcommand', true);
        $commandConfig = new CommandConfig;
        $this->assertSame(2, count($commandConfig->getSubcommandNames()));
    }

    public function testGetSubcommandNamesWhenSubcommandIsDisabled() {
        $commandConfig = new CommandConfig;
        $this->assertSame([], $commandConfig->getSubcommandNames());
    }

    /**
     * @expectedException LogicException
     */
    public function testGetAllByInvalidSubcommand() {
        $commandConfig = new CommandConfig;
        $this->callProtectedMethod($commandConfig, 'getAll', ['unknown']);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testGetAllWhenConfigFileIsMissing() {
        Config::set('hyperframework.cli.command_config_path', 'unknown');
        $commandConfig = new CommandConfig;
        $this->callProtectedMethod($commandConfig, 'getAll');
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testGetAllWhenConfigFileIsInvalid() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'invalid_config_command.php'
        );
        $commandConfig = new CommandConfig;
        $this->callProtectedMethod($commandConfig, 'getAll');
    }

    public function testCustomizeSubcommandConfigRootPath() {
        Config::set('hyperframework.cli.enable_subcommand', true);
        Config::set(
            'hyperframework.cli.command_config_root_path',
            'custom_command_config_root'
        );
        Config::set(
            'hyperframework.cli.subcommand_config_root_path',
            'custom_subcommands_config_root'
        );
        $commandConfig = new CommandConfig;
        $this->assertSame(
            ['root-path-test-child'], $commandConfig->getSubcommandNames()
        );
    }

    public function testCustomizeCommandConfigRootPath() {
        Config::set(
            'hyperframework.cli.command_config_root_path',
            'custom_command_config_root'
        );
        $commandConfig = new CommandConfig;
        $this->assertSame(
            ['name' => 'root-path-test'],
            $this->callProtectedMethod($commandConfig, 'getAll')
        );
    }

    private function mockCommandConfig(
        array $config = [], $subcommandName = null
    ) {
        $result = $this->getMockBuilder('Hyperframework\Cli\CommandConfig')
            ->setMethods(['getAll', 'getSubcommandNames'])->getMock();
        $result->method('getAll')->will(
            $this->returnCallback(function($arg = null)
                use ($config, $subcommandName) {
                    if ($arg === $subcommandName) {
                        return $config;
                    } else {
                        $this->fail('Config is missing.');
                    }
                }
            )
        );
        $result->method('getSubcommandNames')->willReturn(['child']);
        return $result;
    }
}
