<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ResponseHeaderEngineTest extends Base {
    /**
     * @expectedException Hyperframework\Web\CookieException
     */
    public function testInvalidCookieOptionWhenSetCookie() {
        $engine = new ResponseHeaderEngine;
        $engine->setCookie('name', 'value', ['invalid' => 'value']);
    }
}
