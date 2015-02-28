<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;
use Hyperframework\Web\Test\App;

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
        )->getMock();
        $controller->expects($this->once())->method('run');
        $app->expects($this->once())->method('createController')
            ->willReturn($controller);
        $app->expects($this->once())->method('finalize');
        App::setCreateAppCallback(function() use ($app) {
            return $app;
        });
        App::run();
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
    }

    public function testInitializeErrorHandler() {
    }

    public function testCreateApp() {
    }

    public function testGetRouter() {
    }    
}
