<?php
namespace Hyperframework\Web;

class Controller {
    private $app;

    public function __construct($app) {
       $this->app = $app;
    }

    protected static function getParam($name) {
        $this->app->getParam($name);
    }

    protected function hasParam($name) {
        $this->app->hasParam($name);
    }

    protected function setParam($name, $value) {
        $this->app->setParam($name, $value);
    }

    protected function removeParam($name) {
        $this->app->removeParam($name);
    }

    protected function quit() {
        $this->app->quit();
    }

    protected function redirect($url, $statusCode = 302) {
        $this->app->redirect($url, $statusCode);
    }

    protected function getApp() {
        return $this->app;
    }
}
