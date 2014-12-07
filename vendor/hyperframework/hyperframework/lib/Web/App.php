<?php
namespace Hyperframework\Web;

use Exception;

abstract class App {
    private $router;

    public function __construct() {
        $this->router = $this->createRouter();
    }

    public function run() {
        $controller = $this->createController();
        $controller->run();
        $this->finalize();
    }

    public function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        $this->quit();
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    abstract protected function createRouter();

    public function getRouter() {
        return $this->router;
    }

    protected function createController() {
        $router = $this->getRouter();
        if ($router === null) {
           throw new Exception;
        }
        $controllerClass = (string)$router->getControllerClass();
        if ($controllerClass === ''
            || class_exists($controllerClass) === false
        ) {
            throw new Exception;
        }
        return new $controllerClass($this);
    }

    protected function finalize() {}
}
