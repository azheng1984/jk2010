<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class CsrfProtectionEngineTest extends Base {
    public function tearDown() {
        ResponseHeader::setEngine(null);
        parent::tearDown();
    }

    public function testRun() {
        $engine2 = $this->getMock(
            'Hyperframework\Web\ResponseHeaderEngine'
        );
        $engine2->expects($this->once())->method('setCookie');
        ResponseHeader::setEngine($engine2);
        $engine = new CsrfProtectionEngine;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $engine->run();
    }
}
