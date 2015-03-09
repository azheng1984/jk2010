<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ResponseHeaderTest extends Base {
    public function testSetHeader() {
        $this->mockEngineMethod('setHeader');
        ResponseHeader::setHeader('');
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMock(
            'Hyperframework\Web\ResponseHeaderEngine'
        );
        ResponseHeader::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
