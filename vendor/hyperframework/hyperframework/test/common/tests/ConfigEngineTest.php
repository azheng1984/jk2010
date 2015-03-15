<?php
namespace Hyperframework\Common;

use Exception;
use stdClass;
use Hyperframework\Common\Test\Message;
use Hyperframework\Common\Test\TestCase as Base;

class ConfigEngineTest extends Base {
    public function testGet() {
        $engine = new ConfigEngine;
        $engine->set('name', 'value');
        $this->assertSame('value', $engine->get('name'));
    }

    public function testGetReturnDefaultValue() {
        $engine = new ConfigEngine;
        $this->assertSame('default', $engine->get('name', 'default'));
    }

    public function getString() {
        $engine = new ConfigEngine;
        $engine->set('name', 'value');
        $this->assertSame('value', $engine->getString('name'));
    }

    public function testGetStringWhenValueIsInt() {
        $engine = new ConfigEngine;
        $engine->set('name', 1);
        $this->assertSame('1', $engine->getString('name'));
    }

    public function testGetStringWhenValueIsResource() {
        $engine = new ConfigEngine;
        $resource = fopen('php://input', 'r');
        $engine->set('name', $resource);
        try {
            $this->assertTrue(is_string($engine->getString('name')));
        } catch (Exception $e) {
            fclose($resource);
            throw $e;
        }
        fclose($resource);
    }

    public function testGetStringWhenValueIsObject() {
        $engine = new ConfigEngine;
        $engine->set('name', new Message);
        $this->assertSame('message', $engine->getString('name'));
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testGetStringWhenValueIsInvalidObject() {
        $engine = new ConfigEngine;
        $engine->set('name', new stdClass);
        $engine->getString('name');
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testGetStringWhenValueIsArray() {
        $engine = new ConfigEngine;
        $engine->set('name', []);
        $engine->getString('name');
    }

    public function testGetStringReturnDefaultValue() {
        $engine = new ConfigEngine;
        $this->assertSame('default', $engine->getString('name', 'default'));
    }

    public function testGetBoolean() {
        $engine = new ConfigEngine;
        $engine->set('name', true);
        $this->assertTrue($engine->getBoolean('name'));
    }

    public function testGetBooleanReturnDefaultValue() {
        $engine = new ConfigEngine;
        $this->assertTrue($engine->getBoolean('name', true));
    }

    public function testGetBooleanWhenValueIsInt() {
        $engine = new ConfigEngine;
        $engine->set('name', 1);
        $this->assertTrue($engine->getBoolean('name'));
    }
}
