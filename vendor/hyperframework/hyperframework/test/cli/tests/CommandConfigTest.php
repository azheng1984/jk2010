<?php
namespace Hyperframework\Cli;

use Hyperframework\Cli\Test\TestCase as Base;

class CommandConfigTest extends Base {
    public function testGetName() {
        $config = new CommandConfig;
        $this->assertSame('test', $config->getName());
    }
}
