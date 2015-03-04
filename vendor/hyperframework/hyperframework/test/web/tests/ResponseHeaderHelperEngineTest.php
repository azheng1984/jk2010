<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ResponseHeaderHelperEngineTest extends Base {
    /**
     * @expectedException Hyperframework\Web\CookieException
     */
    public function testInvalidCookieOptionWhenSetCookie() {
        $engine = new ResponseHeaderHelperEngine;
        $engine->setCookie('name', 'value', ['invalid' => 'value']);
    }
}
