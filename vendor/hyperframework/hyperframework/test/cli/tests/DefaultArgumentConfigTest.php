<?php
namespace Hyperframework\Cli;

use ReflectionMethod;
use Hyperframework\Cli\Test\TestCase as Base;

class DefaultArgumentConfigTest extends Base {
    public function test() {
        $method = new ReflectionMethod(
            'Hyperframework\Cli\Test\RepeatableArgumentCommand', 'execute'
        );
        $params = $method->getParameters();
        $config = new DefaultArgumentConfig($params[0]);
        $this->assertSame('arg', $config->getName());
        $this->assertFalse($config->isOptional());
        $this->assertTrue($config->isRepeatable());
    }
}
