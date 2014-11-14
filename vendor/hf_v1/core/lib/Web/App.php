<?php
namespace Hyperframework\Web;

class App {
    private $router;

    public function run() {
        $this->initialize();
        $controller = $this->getController();
        $controller->run();
        $this->finalize();
    }

    public function getRouter() {
        return $this->router;
    }

    protected function setRouter($router) {
        $this->router = $router;
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

    protected function initializeRouter() {
        $this->router = new Router($this);
    }

    protected function getController() {
        $router = $this->getRouter();
        $controllerClass = $router->getControllerClass();
        if ($controllerClass === null
            || class_exists($controllerClass) === false
        ) {
            throw new NotFoundException;
        }
        return new $controllerClass($this);
    }

    protected function finalize() {}
}
