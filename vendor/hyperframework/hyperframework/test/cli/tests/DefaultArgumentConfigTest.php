<?php
namespace Hyperframework\Cli;

use ReflectionFunction;
use ReflectionMethod;
use Hyperframework\Cli\Test\TestCase as Base;

class DefaultArgumentConfigTest extends Base {
    public function test() {
        $reflectionFunction = new ReflectionFunction(function(array $args) {});
        $reflectionParameter = $reflectionFunction->getParameters()[0];
        $config = new DefaultArgumentConfig($reflectionParameter);
        $this->assertSame('arg', $config->getName());
        $this->assertTrue($config->isRequired());
        $this->assertTrue($config->isRepeatable());
    }

    public function testNameIncludesMultipleWords() {
        $reflectionFunction = new ReflectionFunction(function($arg__arg) {});
        $reflectionParameter = $reflectionFunction->getParameters()[0];
        $config = new DefaultArgumentConfig($reflectionParameter);
        $this->assertSame('arg-arg', $config->getName());
    }

    public function testNameOfRepeatableArgumentEndsWithCollection() {
        $reflectionFunction = new ReflectionFunction(
            function(array $argCollection) {}
        );
        $reflectionParameter = $reflectionFunction->getParameters()[0];
        $config = new DefaultArgumentConfig($reflectionParameter);
        $this->assertSame('arg', $config->getName());
    }

    public function testNameOfRepeatableArgumentEqualsCollection() {
        $reflectionFunction = new ReflectionFunction(
            function(array $collection) {}
        );
        $reflectionParameter = $reflectionFunction->getParameters()[0];
        $config = new DefaultArgumentConfig($reflectionParameter);
        $this->assertSame('element', $config->getName());
    }

    public function testNameOfRepeatableArgumentEndsWithList() {
        $reflectionFunction = new ReflectionFunction(
            function(array $argList) {}
        );
        $reflectionParameter = $reflectionFunction->getParameters()[0];
        $config = new DefaultArgumentConfig($reflectionParameter);
        $this->assertSame('arg', $config->getName());
    }

    public function testNameOfRepeatableArgumentEndsWithArray() {
        $reflectionFunction = new ReflectionFunction(
            function(array $argArray) {}
        );
        $reflectionParameter = $reflectionFunction->getParameters()[0];
        $config = new DefaultArgumentConfig($reflectionParameter);
        $this->assertSame('arg', $config->getName());
    }
}
