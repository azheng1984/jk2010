<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class CommandParserTest extends Base {
    public function testParse() {
        $this->assertSame(
            ['options' => [], 'arguments' => ['arg']],
            CommandParser::parse(new CommandConfig, ['run', 'arg']));
    }
}
