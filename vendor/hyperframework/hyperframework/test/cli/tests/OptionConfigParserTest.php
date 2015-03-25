<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class OptionConfigParserTest extends Base {
    public function testParse() {
        $result = OptionConfigParser::parse([[
            'name' => 'test',
            'short_name' => 't',
            'repeatable' => true,
            'required' => true,
            'description' => 'description',
            'argument' => [
                'name' => 'arg',
                'required' => false,
                'values' => ['a', 'b'],
            ]
        ]]);
        $optionConfig = $result['t'];
        $this->assertSame('description', $optionConfig->getDescription());
        $this->assertSame('t', $optionConfig->getShortName());
        $this->assertSame('test', $optionConfig->getName());
        $this->assertTrue($optionConfig->isRequired());
        $this->assertTrue($optionConfig->isRepeatable());
        $argumentConfig = $optionConfig->getArgumentConfig();
        $this->assertSame('arg', $argumentConfig->getName());
        $this->assertFalse($argumentConfig->isRequired());
        $this->assertSame(['a', 'b'], $argumentConfig->getValues());
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidConfigs() {
        OptionConfigParser::parse(['config']);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testNameAndShortNameAreAllMissing() {
        OptionConfigParser::parse([[]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidNameType() {
        OptionConfigParser::parse([['name' => true]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidNameFormat() {
        OptionConfigParser::parse([['name' => '']]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidShortNameType() {
        OptionConfigParser::parse([['name' => true]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidShortNameFormat() {
        OptionConfigParser::parse([['name' => '']]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testValuesConflictBetweenNameAndShortName() {
        OptionConfigParser::parse([['name' => 'x', 'short_name' => 'y']]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidDescriptionType() {
        OptionConfigParser::parse([['name' => 'test', 'description' => false]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testDuplicatedOptions() {
        OptionConfigParser::parse([
            ['name' => 'test'],
            ['name' => 'test'],
        ]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidArgumentConfig() {
        OptionConfigParser::parse([['name' => 'test', 'argument' => false]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testArgumentNameIsMissing() {
        OptionConfigParser::parse([['name' => 'test', 'argument' => []]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidArgumentNameType() {
        OptionConfigParser::parse([[
            'name' => 'test', 'argument' => ['name' => true]
        ]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidArgumentNameFormat() {
        OptionConfigParser::parse([[
            'name' => 'test', 'argument' => ['name' => '']
        ]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidArgumentRequiredType() {
         OptionConfigParser::parse([[
            'name' => 'test', 'argument' => ['name' => 'arg', 'required' => '']
        ]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidArgumentValuesType() {
        OptionConfigParser::parse([[
            'name' => 'test', 'argument' => ['name' => 'arg', 'values' => '']
        ]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidValueType() {
        OptionConfigParser::parse([[
            'name' => 'test',
            'argument' => ['name' => 'arg', 'values' => [true]]
        ]]);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidValueFormat() {
        OptionConfigParser::parse([[
            'name' => 'test',
            'argument' => ['name' => 'arg', 'values' => ['']]
        ]]);
    }
}
