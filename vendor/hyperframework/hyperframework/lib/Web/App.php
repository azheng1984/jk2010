<?php
namespace Hyperframework\Web;

use Exception;

abstract class App {
    private $router;
    private $controller;

    public function run() {
        declare(adsf='adf');
//        throw new Exception('\'<>');
        //echo $x;
//        echo  new InternalServerErrorException;
//        try{
//           throw  new InternalServerErrorException;
//        } catch (Exception $e) {
//           echo $e;
//           //throw $e;
//        }
//        $x = new \ErrorException('xx!!');
//        echo $x;
//        exit;
//        
//        var_dump($e->getTraceAsString());
        include('');
        $tag = 'xx x';
        array_shift(explode(' ',$tag));
        $this->initialize();
        $controller = $this->getController();
        $controller->run();
        $this->finalize();
        $tag = 'xx x';
        array_shift(explode(' ',$tag));
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
