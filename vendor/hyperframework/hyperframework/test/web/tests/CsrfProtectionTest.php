<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class CsrfProtectionTest extends Base {
    public function testRun() {
        $this->mockEngineMethod('run');
        CsrfProtection::run();
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMock('Hyperframework\Web\CsrfProtectionEngine');
        CsrfProtection::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
