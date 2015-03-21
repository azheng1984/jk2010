<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class ArgumentConfigParserTest extends Base {
    public function testParse() {
        $result = ArgumentConfigParser::parse(
            [['name' => 'arg', 'required' => false, 'repeatable' => true]]
        );
        $argumentConfig = $result[0];
        $this->assertSame('arg', $argumentConfig->getName());
        $this->assertFalse($argumentConfig->isRequired());
        $this->assertTrue($argumentConfig->isRepeatable());
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidConfigs() {
        ArgumentConfigParser::parse(['config']);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testNameIsMissing() {
        ArgumentConfigParser::parse([[]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidNameType() {
        ArgumentConfigParser::parse([['name' => true]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidNameFormat() {
        ArgumentConfigParser::parse([['name' => '']]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidRequiredType() {
        ArgumentConfigParser::parse([['name' => 'arg', 'required' => '']]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidOptionalArgument() {
        ArgumentConfigParser::parse([
            ['name' => 'arg', 'required' => false],
            ['name' => 'arg2']
        ]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidRepeatableType() {
        ArgumentConfigParser::parse([
            ['name' => 'arg', 'repeatable' => '']
        ]);
    }
}
