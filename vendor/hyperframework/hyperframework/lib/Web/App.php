<?php
namespace Hyperframework\Web;

use Exception;

abstract class App {
    private $router;
    private $controller;

    public function run() {
        $this->initialize();
        $controller = $this->getController();
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

    protected function initialize() {
        $this->initializeRouter();
    }

    abstract protected function initializeRouter();

    protected function setRouter($router) {
        $this->router = $router;
    }

    public function getRouter() {
        return $this->router;
    }

    protected function setController($controller) {
        $this->controller = $controller;
    }

    protected function getController() {
        if ($this->controller === null) {
            $router = $this->getRouter();
            $controllerClass = (string)$router->getControllerClass();
            if ($controllerClass === ''
                || class_exists($controllerClass) === false
            ) {
                throw new Exception;
            }
            $this->setController(new $controllerClass($this));
        }
        return $this->controller;
    }

    protected function finalize() {}
}
