<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class MutuallyExclusiveOptionGroupConfigParserTest extends Base {
    public function test() {
        MutuallyExclusiveOptionGroupConfigParser::parse([], [], null);
    }
}
