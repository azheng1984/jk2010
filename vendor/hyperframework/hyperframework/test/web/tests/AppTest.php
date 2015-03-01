<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;
use Hyperframework\Web\Test\App;
use Hyperframework\Web\Test\Router;

class AppTest extends Base {
    public function testConstruct() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        $app = $this->getMockBuilder('Hyperframework\Web\App')
            ->disableOriginalConstructor()
            ->setMethods(['rewriteRequestMethod', 'checkCsrf'])->getMock();
        $app->expects($this->once())->method('rewriteRequestMethod');
        $app->expects($this->once())->method('checkCsrf');
        $app->__construct(dirname(__DIR__));
        $this->assertNotNull(Config::get('hyperframework.app_root_path'));
    }

    public function testRun() {
//        Config::set('hyperframework.app_root_path', dirname(__DIR__));
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        /*
        Config::set(
            'hyperframework.web.router_class', 'Hyperframework\Web\Test\Router'
        );
        */
        $app = $this->getMockBuilder('Hyperframework\Web\App')
            ->setConstructorArgs([dirname(__DIR__)])
            ->setMethods(['createController', 'finalize'])->getMock();
        $controller = $this->getMockBuilder(
            'Hyperframework\Web\Test\IndexController'
        )->setConstructorArgs([$app])->getMock();
        $controller->expects($this->once())->method('run');
        $app->expects($this->once())->method('createController')
            ->willReturn($controller);
        $app->expects($this->once())->method('finalize');
        App::setCreateAppCallback(function() use ($app) {
            return $app;
        });
        App::run();
        App::setCreateAppCallback(null);
    }

    public function testCheckCsrf() {
        $engine = $this->getMock('Hyperframework\Web\CsrfProtectionEngine');
        $engine->expects($this->once())->method('run');
        CsrfProtection::setEngine($engine);
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        $app = $this->getMockBuilder('Hyperframework\Web\App')
            ->disableOriginalConstructor()
            ->setMethods(['rewriteRequestMethod'])->getMock();
        $app->__construct(dirname(__DIR__));
    }

    public function testCheckCsrfWhenProtectionIsDisabled() {
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $engine = $this->getMock('Hyperframework\Web\CsrfProtectionEngine');
        $engine->expects($this->never())->method('run');
        CsrfProtection::setEngine($engine);
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        $app = $this->getMockBuilder('Hyperframework\Web\App')
            ->disableOriginalConstructor()
            ->setMethods(['rewriteRequestMethod'])->getMock();
        $app->__construct(dirname(__DIR__));
    }

    public function testRewriteRequestMethodUsingHttpHeader() {
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'DELETE';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        $app = $this->getMockBuilder('Hyperframework\Web\App')
            ->disableOriginalConstructor()
            ->setMethods(['checkCsrf'])->getMock();
        $app->__construct(dirname(__DIR__));
        $this->assertSame('DELETE', $_SERVER['REQUEST_METHOD']);
        $this->assertSame('POST', $_SERVER['ORIGINAL_REQUEST_METHOD']);
    }

    public function testRewriteRequestMethodUsingPostField() {
        $_POST['_method'] = 'DELETE';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        $app = $this->getMockBuilder('Hyperframework\Web\App')
            ->disableOriginalConstructor()
            ->setMethods(['checkCsrf'])->getMock();
        $app->__construct(dirname(__DIR__));
        $this->assertSame('DELETE', $_SERVER['REQUEST_METHOD']);
        $this->assertSame('POST', $_SERVER['ORIGINAL_REQUEST_METHOD']);
    }

    public function testCreateController() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $app = $this->getMock('Hyperframework\Web\App', [], [dirname(__DIR__)]);
        $router = $this->getMock('Hyperframework\Web\Test\Router');
        $router->expects($this->once())->method('getControllerClass')
            ->willReturn('Hyperframework\Web\Test\IndexController');
        $app->expects($this->once())->method('getRouter')->willReturn($router);
        $this->assertInstanceOf(
            'Hyperframework\Web\Test\IndexController',
            $this->callProtectedMethod(
                $app,
                'createController'
            )
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testCreateControllerWhenControllerClassIsEmpty() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $app = $this->getMock(
            'Hyperframework\Web\App',
            [], [dirname(__DIR__)]
        );
        $app->expects($this->once())->method('getRouter')
            ->willReturn(new Router);
        var_dump($this->callProtectedMethod($app, 'createController'));
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testCreateControllerWhenControllerClassDoesNotExist() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $app = $this->getMock(
            'Hyperframework\Web\App',
            [],
            [dirname(__DIR__)]
        );
        $router = new Router;
        $router->setControllerClass('Unknown');
        $app->expects($this->once())->method('getRouter')->willReturn($router);
        var_dump($this->callProtectedMethod($app, 'createController'));
    }

    public function testInitializeErrorHandler() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $app = new App(dirname(__DIR__));
        $this->expectOutputString('Hyperframework\Web\Test\ErrorHandler::run');
        $this->callProtectedMethod(
            $app,
            'initializeErrorHandler',
            ['Hyperframework\Web\Test\ErrorHandler']
        );
    }

    public function testCreateApp() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        $this->assertInstanceOf(
            'Hyperframework\Web\App',
            $this->callProtectedMethod('Hyperframework\Web\App', 'createApp')
        );
    }

    public function testGetDefaultRouter() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set(
            'hyperframework.app_root_namespace', 'Hyperframework\Web\Test'
        );
        $app = new App(dirname(__DIR__));
        $this->assertInstanceOf(
            'Hyperframework\Web\Test\Router', $app->getRouter()
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testGetDefaultRouterClassDoesNotExist() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set(
            'hyperframework.app_root_namespace', 'Unknown'
        );
        $app = new App(dirname(__DIR__));
        $app->getRouter();
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testGetCustomRouterClassDoesNotExist() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set('hyperframework.web.router_class', 'Unknown');
        $app = new App(dirname(__DIR__));
        $app->getRouter();
    }

    public function testGetCustomRouter() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.web.csrf_protection.enable', false);
        Config::set(
            'hyperframework.web.router_class', 'Hyperframework\Web\Test\Router'
        );
        $app = new App(dirname(__DIR__));
        $this->assertInstanceOf(
            'Hyperframework\Web\Test\Router', $app->getRouter()
        );
    }
}
