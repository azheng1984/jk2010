<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class RequestTest extends Base {
    public function testSetHeader() {
        $this->mockEngineMethod('getHeaders');
        Request::getHeaders();
    }

    public function testOpenInputStream() {
        $this->mockEngineMethod('openInputStream');
        Request::openInputStream();
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMock('Hyperframework\Web\RequestEngine');
        Request::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
