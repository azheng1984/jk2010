<?php
namespace Hyperframework\Web;

use Hyperframework\Web\Test\Exception;
use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;
use Hyperframework\Web\Test\IndexController;
use Hyperframework\Web\Test\InvalidConstructorController;

class ControllerTest extends Base {
    public function testConstruct() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set(
            'hyperframework.web.router_class',
            'Hyperframework\Web\Test\Router'
        );
        $app = new App(dirname(__DIR__));
        $controller = new IndexController($app);
        $this->assertSame($app, $controller->getApp());
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
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set(
            'hyperframework.web.router_class',
            'Hyperframework\Web\Test\Router'
        );
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
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set(
            'hyperframework.web.router_class',
            'Hyperframework\Web\Test\Router'
        );
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
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set(
            'hyperframework.web.router_class',
            'Hyperframework\Web\Test\Router'
        );
        $app = new App(dirname(__DIR__));
        $router = $app->getRouter();
        $router->setAction('index');
        $router->setController('index');
        $controller = new IndexController($app);
        $this->assertSame('index/index.html.php', $controller->getView());
    }

    public function testRenderView() {
    }

    public function testQuit() {
    }

    public function testRedirect() {
    }

    public function testAddBeforeFilter() {
    }

    public function testAddAfterFilter() {
    }

    public function testAddAroundFilter() {
    }

    /**
     * @expectedException Hyperframework\Common\NotSupportedException
     */
    public function testAddAroundFilterWhenNotSupported() {
        if (version_compare(phpversion(), '5.5.0', '>=')) {
            $this->markTestSkipped('PHP 5.4 is required.');
            return;
        }
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set(
            'hyperframework.web.router_class',
            'Hyperframework\Web\Test\Router'
        );
        $app = new App(dirname(__DIR__));
        $controller = new IndexController($app);
        $controller->addAroundFilter(function() {});
    }
}
