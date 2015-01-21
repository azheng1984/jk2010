<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class RouterTest extends Base {
    private $router;

    protected function setUp() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $this->router = $this->getMockForAbstractClass(
            'Hyperframework\Web\Router',
            [new App],
            '',
            false
        );
    }

    protected function tearDown() {
    }

    public function testMatchRootPath() {
        $this->assertTrue($this->match('/', ['/']));
    }

    public function testMatchMethod() {
        $this->assertTrue($this->match('/', ['/', ['methods' => 'get']]));
    }

    public function testFailToMatchMethod() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->match('/', ['/', ['methods' => 'get']]));
    }

    public function testFailToMatchMethods() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse(
            $this->match('/', ['/', ['methods' => ['get', 'put']]])
        );
    }

    public function testMatchTextWithExtraRule() {
        $this->assertFalse(
            $this->match('/', ['/', ['extra' => function() {
                return false;
            }]])
        );
    }

    public function testOptionalSegment() {
        $this->assertTrue(
            $this->match('/document/name', ['/document(/name)'])
        );
    }

    public function testOptionalSegmentWithFormat() {
        $this->assertTrue(
            $this->match('/document/name.jpg', ['/document(/name)', ['formats' => 'jpg']])
        );
    }

    public function testOptionalDynamicSegment() {
        $this->assertTrue(
            $this->match('/document/name/extra', ['/document(/:name)/extra'])
        );
        $this->assertSame('name', $this->router->getParam('name'));
    }

    public function testDynamicSegment() {
        $this->assertTrue(
            $this->match('/document/name/extra', ['/document/:name/:extra'])
        );
        $this->assertSame('name', $this->router->getParam('name'));
        $this->assertSame('extra', $this->router->getParam('extra'));
    }

    public function testWildcardSegment() {
        $this->assertTrue(
            $this->match('/document/name/extra/end', ['/document/*name/end'])
        );
        $this->assertSame('name/extra', $this->router->getParam('name'));
    }

    public function testOptionalWildcardSegment() {
        $this->assertTrue(
            $this->match('/document/name/extra', ['/document(/*name)'])
        );
        $this->assertSame('name/extra', $this->router->getParam('name'));
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     */
    public function testInvalidPatternForMatchWithNumberSign() {
        $this->match('/', ['#']);
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     * @expectedExceptionMessage Already matched.
     */
    public function testAlreadyMatched() {
        $this->match('/', ['/']);
        $this->match('/', ['/']);
    }

    private function match($uri, $args) {
        $_SERVER['REQUEST_URI'] = $uri;
        return $this->callProtectedMethod($this->router, 'match', $args);
    }

    private function matchScope($uri, $args) {
        $_SERVER['REQUEST_URI'] = $uri;
        return $this->callProtectedMethod($this->router, 'matchScope', $args);
    }
}
