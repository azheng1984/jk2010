<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class ControllerTest extends Base {
    public function testRun() {
        $this->expectOutputString('view: index/index');
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
        $controller = new Controller($app);
        $controller->run();
    }
}
