<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class ArgumentConfigParserTest extends Base {
    public function testParse() {
        $result = ArgumentConfigParser::parse(
            [['name' => 'arg', 'required' => false, 'repeatable' => true]], null
        );
        $argumentConfig = $result[0];
        $this->assertSame('arg', $argumentConfig->getName());
        $this->assertFalse($argumentConfig->isRequired());
        $this->assertTrue($argumentConfig->isRepeatable());
    }
}
