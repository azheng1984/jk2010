<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class RouterTest extends Base {
    public function testMatch() {
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $router = $this->getMockForAbstractClass(
            'Hyperframework\Web\Router',
            [new App],
            '',
            false
        );
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertTrue($this->callProtectedMethod($router, 'match', ['/']));
    }

    public function testMatch() {
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $router = $this->getMockForAbstractClass(
            'Hyperframework\Web\Router',
            [new App],
            '',
            false
        );
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertTrue($this->callProtectedMethod($router, 'match', ['/']));
    }
}
