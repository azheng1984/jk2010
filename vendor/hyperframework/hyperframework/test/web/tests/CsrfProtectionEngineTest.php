<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class CsrfProtectionEngineTest extends Base {
    public function tearDown() {
        ResponseHeaderHelper::setEngine(null);
        parent::tearDown();
    }

    public function testRun() {
        $engine2 = $this->getMock(
            'Hyperframework\Web\ResponseHeaderHelperEngine'
        );
        ResponseHeaderHelper::setEngine($engine2);
        $engine = new CsrfProtectionEngine;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $engine->run();
    }
}
