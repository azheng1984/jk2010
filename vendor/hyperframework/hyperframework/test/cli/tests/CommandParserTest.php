<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;
use Hyperframework\Cli\Test\TestCase as Base;

class CommandParserTest extends Base {
    public function testParseCommand() {
        $this->assertSame(
            [
                'options' => [],
                'arguments' => ['arg']
            ],
            CommandParser::parse(new CommandConfig, ['run', 'arg'])
        );
    }

    public function testParseSubcommand() {
        Config::set('hyperframework.cli.enable_subcommand', true);
        $this->assertSame(
            [
                'global_options' => [],
                'subcommand_name' => 'child',
                'options' => [],
                'arguments' => ['arg']
            ],
            CommandParser::parse(new CommandConfig, ['run', 'child',  'arg'])
        );
    }

    /**
     * @expectedException Hyperframework\Cli\CommandParsingException
     */
    public function testParseWhenSubcommandDoesNotExist() {
        Config::set('hyperframework.cli.enable_subcommand', true);
        CommandParser::parse(new CommandConfig, ['run', 'unknown-subcommand']);
    }

    /**
     * @expectedException Hyperframework\Cli\CommandParsingException
     */
    public function testParseWhenGlobalOptionNameIsInvalid() {
        Config::set('hyperframework.cli.enable_subcommand', true);
        CommandParser::parse(new CommandConfig, ['run', '--', 'child', 'arg']);
    }

    /**
     * @expectedException Hyperframework\Cli\CommandParsingException
     */
    public function testInvalidOptionName() {
        CommandParser::parse(new CommandConfig, ['run', '--test']);
    }

    /**
     * @expectedException Hyperframework\Cli\CommandParsingException
     */
    public function testInvalidOptionShortName() {
        CommandParser::parse(new CommandConfig, ['run', '-x']);
    }

    /**
     * @expectedException Hyperframework\Cli\CommandParsingException
     */
    public function testParseShortOptionWhenOptionArgumentIsInvaild() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'option_argument_is_required_command.php'
        );
        CommandParser::parse(new CommandConfig, ['run', '-t']);
    }

    /**
     * @expectedException Hyperframework\Cli\CommandParsingException
     */
    public function testParseLongOptionWhenOptionArgumentIsInvaild() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'option_argument_is_required_command.php'
        );
        CommandParser::parse(new CommandConfig, ['run', '--test']);
    }

    public function testParseRepeatableShortOption() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'option_is_repeatable_command.php'
        );
        $this->assertSame(
            [
                'options' => ['test' => [true, true]],
                'arguments' => []
            ],
            CommandParser::parse(new CommandConfig, ['run', '-tt'])
        );
    }

    public function testParseRepeatableLongOption() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'option_is_repeatable_command.php'
        );
        $this->assertSame(
            [
                'options' => ['test' => [true, true]],
                'arguments' => []
            ],
            CommandParser::parse(new CommandConfig, ['run', '--test', '--test'])
        );
    }

    public function testParseOptionWhichHasNameAndShortName() {
        Config::set(
            'hyperframework.cli.command_config_path',
            'option_has_name_and_short_name_command.php'
        );
        $this->assertSame(
            [
                'options' => ['test' => [true, true]],
                'arguments' => []
            ],
            CommandParser::parse(new CommandConfig, ['run', '-tt'])
        );
    }

    private function parse() {
        $parser = new CommandParser;
        $parser->parse();
    }

//    public function testParseSubcommand() {
//        Config::set('hyperframework.cli.enable_subcommand', true);
//        $this->assertSame(
//            ['options' => [], 'arguments' => ['arg']],
//            CommandParser::parse(new CommandConfig, ['run', 'arg']));
//
//        CommandParser::parse(new CommandConfig, ['run', 'child', 'arg']);
//    }
}
