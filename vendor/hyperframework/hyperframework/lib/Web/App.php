<?php
namespace Hyperframework\Web;

use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Common\NamespaceBuilder;

class App {
    private $router;

    public function run() {
        $controller = $this->createController();
        $controller->run();
        $this->finalize();
    }

    public function getRouter() {
        if ($this->router === null) {
            $class = Config::getString(
                'hyperframework.web.router_class', ''
            );
            if ($class === '') {
                $namespace = Config::get(
                    'hyperframework.app_root_namespace', ''
                );
                $class = 'Router';
                if ($namespace !== '' && $namespace !== '\\') {
                    NamespaceBuilder::prepend($class, $namespace);
                }
                if (class_exists($class) === false) {
                    throw new Exception($class . ' not found');
                }
            }
            $this->router = new $class($this);
        }
        return $this->router;
    }

    public function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        $this->quit();
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function createController() {
        $router = $this->getRouter();
        $class = (string)$router->getControllerClass();
        var_dump($class);
        if ($class === '' || class_exists($class) === false) {
            throw new Exception;
        }
        return new $class($this);
    }

    protected function finalize() {}
}
