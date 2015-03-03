<?php
namespace Hyperframework\Web;

use Hyperframework\Web\Test\Exception;
use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;
use Hyperframework\Web\Test\IndexController;
use Hyperframework\Web\Test\InvalidConstructorController;
use Hyperframework\Common\NotSupportedException;

class ControllerTest extends Base {
    protected function setUp() {
        parent::setUp();
        Config::set(
            'hyperframework.web.router_class',
            'Hyperframework\Web\Test\FakeRouter'
        );
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWhenAppArgumentIsInvalid() {
        new IndexController(null);
    }

    /**
     * @expectedException LogicException
     */
    public function testGetAppWhenParentConstructorOfControllerIsNotCalled() {
        $controller = new InvalidConstructorController;
        $controller->getApp();
    }

    public function testRun() {
        $app = new App(dirname(__DIR__));
        $router = $app->getRouter();
        $router->setAction('index');
        $router->setActionMethod('doIndexAction');
        $router->setController('index');
        $controller = $this->getMockBuilder(
            'Hyperframework\Web\Test\IndexController'
        )->setConstructorArgs([$app])
            ->setMethods(['handleAction', 'finalize'])->getMock();
        $recorder = [];
        $controller->addBeforeFilter(function() use (&$recorder) {
            $recorder[] = 'before';
        });
        $controller->addAfterFilter(function() use (&$recorder) {
            $recorder[] = 'after';
        });
        $controller->expects($this->once())->method('handleAction')
            ->will($this->returnCallback(function() use (&$recorder) {
                $recorder[] = 'handle_action';
            }));
        $controller->expects($this->once())->method('finalize')
            ->will($this->returnCallback(function() use (&$recorder) {
                $recorder[] = 'finalize';
            }));
        $controller->run();
        $this->assertSame(['before', 'handle_action', 'after', 'finalize'], $recorder);
    }

    /**
     * @requires PHP 5.5
     */
    public function testRunWhenExceptionIsThrown() {
        $app = new App(dirname(__DIR__));
        $router = $app->getRouter();
        $router->setAction('index');
        $router->setActionMethod('doIndexAction');
        $router->setController('index');
        $controller = $this->getMockBuilder(
            'Hyperframework\Web\Test\IndexController'
        )->setConstructorArgs([$app])
            ->setMethods(['handleAction', 'finalize'])->getMock();
        $isCaught = false;
        $controller->addAroundFilter(function() use (&$isCaught) {
            try {
                yield;
            } catch (Exception $e) {
                $isCaught = true;
            }
        });
        $controller->addBeforeFilter(function() {
            throw new Exception;
        });
        $controller->run();
        $this->assertTrue($isCaught);
    }

    public function testGetView() {
        $app = new App(dirname(__DIR__));
        $router = $app->getRouter();
        $router->setAction('index');
        $router->setController('index');
        $controller = new IndexController($app);
        $this->assertSame('index/index.html.php', $controller->getView());
    }

    public function testRenderView() {
        $this->expectOutputString('view: index/index');
        $app = new App(dirname(__DIR__));
        $router = $app->getRouter();
        $router->setAction('index');
        $router->setController('index');
        $controller = new IndexController($app);
        $controller->renderView();
    }

    public function testQuit() {
        $isExitCalled = false;
        Config::set('hyperframework.exit_function', function() use (&$isExitCalled) {
            $isExitCalled = true;
        });
        $app = new App(dirname(__DIR__));
        $controller = $this->getMockBuilder(
            'Hyperframework\Web\Test\IndexController'
        )->setConstructorArgs([$app])
            ->setMethods(['handleAction', 'finalize'])->getMock();
        $controller = new IndexController($app);
        $controller->quit();
        $this->assertTrue($isExitCalled);
    }

    public function testRedirect() {
        $app = new App(dirname(__DIR__));
        $controller = $this->getMockBuilder(
            'Hyperframework\Web\Test\IndexController'
        )->setConstructorArgs([$app])
            ->setMethods(['handleAction', 'quit'])->getMock();
        $controller->expects($this->once())->method('quit');
        $engine = $this->getMock('Hyperframework\Web\ResponseHeaderHelperEngine');
        $engine->expects($this->once())->method('setHeader')->with(
            'Location: /', true, 302
        );
        ResponseHeaderHelper::setEngine($engine);
        $controller->redirect('/');
    }

    public function testAddBeforeFilter() {
        $app = new App(dirname(__DIR__));
        $router = $app->getRouter();
        $router->setAction('index');
        $router->setActionMethod('doIndexAction');
        $router->setController('index');
        $controller = $this->getMockBuilder(
            'Hyperframework\Web\Test\IndexController'
        )->setConstructorArgs([$app])->setMethods(['handleAction'])->getMock();
        $isCalled = false;
        $controller->addBeforeFilter(function() use (&$isCalled) {
            $isCalled = true;
        });
        $controller->run();
        $this->assertTrue($isCalled);
    }

    public function testAddAfterFilter() {
        $app = new App(dirname(__DIR__));
        $router = $app->getRouter();
        $router->setAction('index');
        $router->setActionMethod('doIndexAction');
        $router->setController('index');
        $controller = $this->getMockBuilder(
            'Hyperframework\Web\Test\IndexController'
        )->setConstructorArgs([$app])->setMethods(['handleAction'])->getMock();
        $isCalled = false;
        $controller->addAfterFilter(function() use (&$isCalled) {
            $isCalled = true;
        });
        $controller->run();
        $this->assertTrue($isCalled);
    }

    public function testAddAroundFilter() {
        $app = new App(dirname(__DIR__));
        $router = $app->getRouter();
        $router->setAction('index');
        $router->setActionMethod('doIndexAction');
        $router->setController('index');
        $controller = $this->getMockBuilder(
            'Hyperframework\Web\Test\IndexController'
        )->setConstructorArgs([$app])->setMethods(['handleAction'])->getMock();
        $recorder = [];
        $controller->addAroundFilter(function() use (&$recorder) {
            $recorder[] = 'before';
            yield;
            $recorder[] = 'after';
        });
        $controller->run();
        $this->assertSame(['before', 'after'], $recorder);
    }

    /**
     * @expectedException Hyperframework\Common\NotSupportedException
     */
    public function testAddAroundFilterWhenNotSupported() {
        if (version_compare(phpversion(), '5.5.0', '>=')) {
            throw new NotSupportedException;
        }
        $app = new App(dirname(__DIR__));
        $controller = new IndexController($app);
        $controller->addAroundFilter(function() {});
    }
}
