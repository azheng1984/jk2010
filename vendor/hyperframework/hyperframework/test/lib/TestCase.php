<?php
namespace Hyperframework\Test;

use ReflectionClass;
use PHPUnit_Framework_TestCase as Base;

class TestCase extends Base {
    protected function callProtectedMethod(
        $objectOrClass, $method, $args = []
    ) {
        $class = $objectOrClass;
        $object = null;
        if (is_object($objectOrClass)) {
            $class = get_class($objectOrClass);
            $object = $objectOrClass;
        }
        $reflectionClass = new ReflectionClass($class);
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs($object, $args);
    }
}
