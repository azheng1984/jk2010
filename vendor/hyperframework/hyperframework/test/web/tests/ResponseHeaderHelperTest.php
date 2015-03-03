<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ResponseHeaderHelperTest extends Base {
    public function testSetHeader() {
        $this->mockEngineMethod('setHeader');
        ResponseHeaderHelper::setHeader('');
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMock(
            'Hyperframework\Web\ResponseHeaderHelperEngine'
        );
        ResponseHeaderHelper::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
