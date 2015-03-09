<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class CsrfProtectionTest extends Base {
    public function tearDown() {
        CsrfProtection::setEngine(null);
        parent::tearDown();
    }

    public function testRun() {
        $this->mockEngineMethod('run');
        CsrfProtection::run();
    }

    public function testGetToken() {
        $this->mockEngineMethod('getToken')->willReturn(true);
        $this->assertTrue(CsrfProtection::getToken());
    }

    public function testGetTokenName() {
        $this->mockEngineMethod('getTokenName')->willReturn(true);
        $this->assertTrue(CsrfProtection::getTokenName());
    }

    public function testCustomEngine() {
        Config::set(
            'hyperframework.web.csrf_protection.engine_class',
            'Hyperframework\Web\Test\CsrfProtectionEngine'
        );
        $this->assertInstanceOf(
            'Hyperframework\Web\Test\CsrfProtectionEngine',
            CsrfProtection::getEngine()
        );
    }

    public function testIsEnabled() {
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $this->assertFalse(CsrfProtection::isEnabled());
    }

    private function mockEngineMethod($method) {
        $engine = $this->getMock('Hyperframework\Web\CsrfProtectionEngine');
        CsrfProtection::setEngine($engine);
        return $engine->expects($this->once())->method($method);
    }
}
