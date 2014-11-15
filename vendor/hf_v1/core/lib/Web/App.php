<?php
namespace Hyperframework\Web;

use Exception;

class App {
    private $router;
    private $controller;

    public function run() {
        $this->initialize();
        $controller = $this->getController();
        $controller->run();
        $this->finalize();
    }

    public function getRouter() {
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

    protected function initialize() {
        $this->initializeRouter();
        $this->initializeController();
    }

    protected function initializeRouter() {
        $this->setRouter(new Router($this));
    }

    protected function initializeController() {
        $router = $this->getRouter();
        $controllerClass = $router->getControllerClass();
        if ($controllerClass === null
            || class_exists($controllerClass) === false
        ) {
            throw new Exception;
        }
        $this->setController(new $controllerClass($this));
    }

    protected function setRouter($router) {
        $this->router = $router;
    }

    protected function setController($controller) {
        $this->controller = $controller;
    }

    protected function getController() {
        return $this->controller;
    }

    protected function finalize() {}
}
