<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class RouterTest extends Base {
    private $router;

    protected function setUp() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $this->resetRouter();
        $_SERVER['REQUEST_URI'] = '/';
    }

    private function resetRouter() {
        $this->router = $this->getMockForAbstractClass(
            'Hyperframework\Web\Router',
            [new App],
            '',
            false
        );
    }

    protected function tearDown() {
    }

    public function testMatchFormatInPattern() {
        $_SERVER['REQUEST_URI'] = '/document/id.format';
        $this->assertTrue($this->match(':controller/:id(.:format)'));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('id', $this->router->getParam('id'));
        $this->assertSame('format', $this->router->getParam('format'));
    }

    public function testMatchFormat() {
        $_SERVER['REQUEST_URI'] = '/document/id.format';
        $this->assertTrue($this->match(':controller/:id', ['format' => true]));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('id', $this->router->getParam('id'));
        $this->assertSame('format', $this->router->getParam('format'));
    }

    public function testMatchOptionalFormat() {
        $_SERVER['REQUEST_URI'] = '/document/id.html';
        $this->assertTrue($this->match(':controller/:id', [
            'format' => true,
            'default_format' => 'unknown'
        ]));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('id', $this->router->getParam('id'));
        $this->assertSame('html', $this->router->getParam('format'));
    }

    public function testMatchFormatNotAllowed() {
        $_SERVER['REQUEST_URI'] = '/document/id.unknown';
        $this->assertFalse($this->match(':controller/:id', [
            'format' => 'html'
        ]));
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

    public function testMatchRootPath() {
        $this->assertTrue($this->match('/'));
    }

    public function testMatchMethod() {
        $this->assertTrue($this->match('/', ['methods' => ['get']]));
    }

    public function testMatchSectionsInSegment() {
        $_SERVER['REQUEST_URI'] = '/s1-s2/s3.x';
        $this->assertTrue($this->match('/:s1-:s2/:s3'));
        $this->assertSame('s1', $this->router->getParam('s1'));
        $this->assertSame('s2', $this->router->getParam('s2'));
        $this->assertSame('s3.x', $this->router->getParam('s3'));
    }

    public function testFailToMatchMethod() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->match('/', ['methods' => ['get']]));
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
            $this->match('document(/name)', ['format' => 'format'])
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

    public function testMatchResourceShowAction() {
        $_SERVER['REQUEST_URI'] = '/document';
        $this->assertTrue($this->matchResource('document'));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('show', $this->router->getAction());
    }

    public function testMatchResourceNewAction() {
        $_SERVER['REQUEST_URI'] = '/document/new';
        $this->assertTrue($this->matchResource('document'));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('new', $this->router->getAction());
    }

    public function testMatchResourceEditAction() {
        $_SERVER['REQUEST_URI'] = '/document/edit';
        $this->assertTrue($this->matchResource('document'));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('edit', $this->router->getAction());
    }

    public function testMatchResourceUpdateAction() {
        $_SERVER['REQUEST_URI'] = '/document';
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $this->assertTrue($this->matchResource('document'));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('update', $this->router->getAction());
    }

    public function testMatchResourceDeleteAction() {
        $_SERVER['REQUEST_URI'] = '/document';
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $this->assertTrue($this->matchResource('document'));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('delete', $this->router->getAction());
    }

    public function testMatchResourceInScope() {
        $_SERVER['REQUEST_URI'] = '/admin/document';
        $this->assertTrue(
            $this->matchScope('admin', function() {
                return $this->matchResource('document');
            })
        );
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('show', $this->router->getAction());
    }

    public function testMatchResourceCreateAction() {
        $_SERVER['REQUEST_URI'] = '/document';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue($this->matchResource('document'));
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('create', $this->router->getAction());
    }

    public function testMatchResources() {
        $_SERVER['REQUEST_URI'] = '/documents/1';
        $this->assertTrue($this->matchResources('documents'));
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     */
    public function testMatchResourceFailedByIdSegment() {
        $this->matchResources(':id/document');
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     */
    public function testMatchResourceFailedByIdOption() {
        $this->matchResources('document', [':id' => '\d+']);
    }

    public function testMatchResourcesFailedWithDisableElementAction() {
        $_SERVER['REQUEST_URI'] = '/documents/1';
        $this->assertFalse($this->matchResources(
            'documents', ['element_actions' => false]
        ));
    }

    public function testMatchResourcesFailedWithDisableCollectionAction() {
        $_SERVER['REQUEST_URI'] = '/documents';
        $this->assertFalse(
            $this->matchResources(
                'documents', ['collection_actions' => false]
            )
        );
    }

    public function testMatchResourcesWithCustomCollectionAction() {
        $_SERVER['REQUEST_URI'] = '/documents/preview';
        $this->assertTrue(
            $this->matchResources(
                'documents', ['collection_actions' => ['preview']]
            )
        );
    }

    public function testMatchResourcesWithCustomDefaultAction() {
        $_SERVER['REQUEST_URI'] = '/documents/preview';
        $this->assertTrue(
            $this->matchResources(
                'documents', ['default_actions' => ['preview']]
            )
        );
        $this->resetRouter();
        $_SERVER['REQUEST_URI'] = '/documents';
        $this->assertFalse(
            $this->matchResources(
                'documents', ['default_actions' => ['preview']]
            )
        );
    }

    public function testMatchResourcesWithCustomElementAction() {
        $_SERVER['REQUEST_URI'] = '/documents/1/preview';
        $this->assertTrue(
            $this->matchResources(
                'documents', ['element_actions' => ['preview']]
            )
        );
    }

    public function testMatchResourcesWithRewriteCollectionAction() {
        $_SERVER['REQUEST_URI'] = '/documents';
        $this->assertFalse(
            $this->matchResources(
                'documents', ['collection_actions' => ['preview']]
            )
        );
    }

    public function testMatchResourcesWithRewriteElementAction() {
        $_SERVER['REQUEST_URI'] = '/documents/1';
        $this->assertFalse(
            $this->matchResources(
                'documents', ['element_actions' => ['preview']]
            )
        );
    }

    public function testMatchResourcesWithAddExtraCollectionAction() {
        $_SERVER['REQUEST_URI'] = '/documents';
        $this->assertTrue(
            $this->matchResources(
                'documents', ['extra_collection_actions' => ['preview']]
            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMatchResourceInvalidPattern() {
        $this->matchResource(true);
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     */
    public function testMatchResourceInvalidOption() {
        $this->matchResource('', ['actions' => true]);
    }

    public function testMatchResourceWithCustomActions() {
        $_SERVER['REQUEST_URI'] = '/document/preview';
        $this->assertTrue(
            $this->matchResource('document', ['actions' => ['preview']])
        );
    }

    public function testMatchResourceWithExtraActions() {
        $_SERVER['REQUEST_URI'] = '/document/preview';
        $this->assertTrue(
            $this->matchResource('document', ['extra_actions' => ['preview']])
        );
    }

    public function testMatchResourceWithIgnoredActions() {
        $_SERVER['REQUEST_URI'] = '/document';
        $this->assertFalse(
            $this->matchResource('document', ['ignored_actions' => ['show']])
        );
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     */
    public function testMatchResourceWithInvalidActionMethodName() {
        $_SERVER['REQUEST_URI'] = '/document/preview';
        $this->matchResource('document', ['actions' => ['preview' => true]]);
    }

    /**
     * @expectedException Hyperframework\Web\RoutingException
     */
    public function testMatchResourceWithInvalidActionPath() {
        $_SERVER['REQUEST_URI'] = '/document/preview';
        $this->matchResource(
            'document',
            ['actions' => ['preview' => ['GET', true]]]
        );
    }

    public function testMatchResourceFailedByMethodNotMatched() {
        $_SERVER['REQUEST_URI'] = '/document/edit';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->matchResource('document'));
    }

    public function testMatchResourceWithActionExtraOption() {
        $_SERVER['REQUEST_URI'] = '/document/edit';
        $this->assertFalse($this->matchResource(
            'document',
            [
                'actions' => [
                    'edit' => ['extra' => function() {
                        return false;
                    }]
                ],
                'extra' => function() {
                    return true;
                }
            ]
        ));
    }

    public function testMatchResourceWithActionFormatOption() {
        $_SERVER['REQUEST_URI'] = '/document/edit.format';
        $this->assertTrue($this->matchResource(
            'document',
            [
                'actions' => [
                    'edit' => ['format' => 'format']
                ],
                'format' => 'unknown'
            ]
        ));
    }

    public function testMatchResourceWithPrefix() {
        $_SERVER['REQUEST_URI'] = '/module/document/edit';
        $this->assertTrue($this->matchResource(':module/document'));
        $this->assertSame('module', $this->router->getModule());
        $this->assertSame('document', $this->router->getController());
        $this->assertSame('edit', $this->router->getAction());
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
