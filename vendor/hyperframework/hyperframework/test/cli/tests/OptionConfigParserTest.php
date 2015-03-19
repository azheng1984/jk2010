<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class OptionConfigParserTest extends Base {
    public function testParse() {
        $result = OptionConfigParser::parse(
            [['short_name' => 't', 'description' => 'description']], null
        );
        $config = $result['t'];
        $this->assertSame('description', $config->getDescription());
        $this->assertSame('t', $config->getShortName());
    }
}
