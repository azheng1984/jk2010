<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class ArgumentConfigParserTest extends Base {
    public function testParse() {
        $result = ArgumentConfigParser::parse(['[<arg>...]']);
        $argumentConfig = $result[0];
        $this->assertSame('arg', $argumentConfig->getName());
        $this->assertTrue($argumentConfig->isOptional());
        $this->assertTrue($argumentConfig->isRepeatable());
    }
}
