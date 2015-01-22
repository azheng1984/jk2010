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
        $_SERVER['REQUEST_URI'] = '/';
    }

    protected function tearDown() {
    }

    public function testMatchRootPath() {
        $this->assertTrue($this->match('/'));
    }

    public function testMatchMethod() {
        $this->assertTrue($this->match('/', ['methods' => 'get']));
    }

    public function testFailToMatchMethod() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->match('/', ['methods' => 'get']));
    }

    public function testFailToMatchMethods() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse(
            $this->match('/', ['methods' => ['get', 'put']])
        );
    }

    public function testMatchTextWithExtraRule() {
        $this->assertFalse(
            $this->match('/', ['extra' => function() {
                return false;
            }])
        );
    }

    public function testMatchWithExtraRule() {
        $_SERVER['REQUEST_URI'] = '/document';
        $this->assertFalse(
            $this->match(':section', ['extra' => function() {
                return false;
            }])
        );
    }

    public function testMatchWithExtraRules() {
        $_SERVER['REQUEST_URI'] = '/document';
        $this->assertFalse(
            $this->match(':section', ['extra' => [function() {
                return true;
            }, function() {
                return false;
            }]])
        );
    }

    public function testOptionalSegment() {
        $_SERVER['REQUEST_URI'] = '/document/name';
        $this->assertTrue(
            $this->match('document(/name)')
        );
    }

    public function testOptionalSegmentWithFormat() {
        $_SERVER['REQUEST_URI'] = '/document/name.format';
        $this->assertTrue(
            $this->match('document(/name)', ['formats' => 'format'])
        );
    }

    public function testOptionalDynamicSegment() {
        $_SERVER['REQUEST_URI'] = '/document/name/extra';
        $this->assertTrue(
            $this->match('document(/:name)/extra')
        );
        $this->assertSame('name', $this->router->getParam('name'));
    }

    public function testDynamicSegment() {
        $_SERVER['REQUEST_URI'] = '/document/name/extra';
        $this->assertTrue(
            $this->match('document/:name/:extra')
        );
        $this->assertSame('name', $this->router->getParam('name'));
        $this->assertSame('extra', $this->router->getParam('extra'));
    }

    public function testWildcardSegment() {
        $_SERVER['REQUEST_URI'] = '/document/name/extra/end';
        $this->assertTrue(
            $this->match('document/*name/end')
        );
        $this->assertSame('name/extra', $this->router->getParam('name'));
    }

    public function testOptionalWildcardSegment() {
        $_SERVER['REQUEST_URI'] = '/document/name/extra';
        $this->assertTrue(
            $this->match('document(/*name)')
        );
        $this->assertSame('name/extra', $this->router->getParam('name'));
    }

    public function testMatchModuleAndControllerAndAction() {
        $_SERVER['REQUEST_URI'] = '/module/controller/action';
        $this->assertTrue(
            $this->match('((:module)/:controller)/:action')
        );
        $this->assertSame('module', $this->router->getModule());
        $this->assertSame('controller', $this->router->getController());
        $this->assertSame('action', $this->router->getAction());
    }

    public function testInvalidModule() {
        $_SERVER['REQUEST_URI'] = '/1';
        $this->assertFalse(
            $this->match(':module')
        );
    }

    public function testInvalidController() {
        $_SERVER['REQUEST_URI'] = '/1';
        $this->assertFalse(
            $this->match(':controller')
        );
    }

    public function testInvalidAction() {
        $_SERVER['REQUEST_URI'] = '/1';
        $this->assertFalse(
            $this->match(':action')
        );
    }

    public function testMatchInScope() {
        $_SERVER['REQUEST_URI'] = '/document/name';
        $this->assertTrue($this->matchScope('document', function() {
            $this->assertTrue($this->match(':name'));
        }));
        $this->assertSame('name', $this->router->getParam('name'));
    }

    public function testMatchInScopeWithSlash() {
        $_SERVER['REQUEST_URI'] = '/document/name';
        $this->assertTrue($this->matchScope('/document/', function() {
            $this->assertTrue($this->match('/:name/'));
        }));
        $this->assertSame('name', $this->router->getParam('name'));
    }

    public function testMatchResource() {
        $_SERVER['REQUEST_URI'] = '/document/edit';
        $this->assertTrue($this->matchResources('document'));
    }

    public function testMatchResources() {
        $_SERVER['REQUEST_URI'] = '/documents/123';
        $this->assertTrue($this->matchResources('documents'));
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     */
    public function testInvalidPatternForMatchWithNumberSign() {
        $this->match('#');
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     * @expectedExceptionMessage Already matched.
     */
    public function testAlreadyMatched() {
        $this->match('/');
        $this->match('/');
    }

    private function match($pattern, $options = null) {
        $args = [$pattern, $options];
        return $this->callProtectedMethod($this->router, 'match', $args);
    }

    private function matchScope($path, $callback) {
        $args = [$path, $callback];
        return $this->callProtectedMethod($this->router, 'matchScope', $args);
    }

    private function matchResource($pattern, $options = null) {
        $args = [$pattern, $options];
        return $this->callProtectedMethod($this->router, 'matchResource', $args);
    }

    private function matchResources($pattern, $options = null) {
        $args = [$pattern, $options];
        return $this->callProtectedMethod(
            $this->router, 'matchResources', $args
        );
    }
}
