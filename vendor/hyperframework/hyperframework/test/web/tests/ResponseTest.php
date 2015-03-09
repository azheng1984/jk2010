<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ResponseTest extends Base {
    public function testSetHeader() {
        $this->mockEngineMethod('setHeader');
        Response::setHeader('');
    }

    public function testRemoveAllHeaders() {
        $this->mockEngineMethod('removeAllHeaders');
        Response::removeAllHeaders();
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMock('Hyperframework\Web\ResponseEngine');
        Response::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
