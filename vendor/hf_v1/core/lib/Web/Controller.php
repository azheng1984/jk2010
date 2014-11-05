<?php
namespace Hyperframework\Web;

class Controller {
    private $app;

    public function __construct($app) {
       $this->app = $app;
    }

    protected static function getRouteParam($name) {
        $this->app->getRouteParam($name);
    }

    protected static function getRouteParams() {
        $this->app->getRouteParams();
    }

    protected function hasRouteParam($name) {
        $this->app->hasRouteParam($name);
    }

    protected function disableView() {
        $this->app->disableView();
    }

    protected function setView($value) {
        $this->app->setView($value);
    }

    protected function redirect($url, $statusCode = 302) {
        $this->app->redirect($url, $statusCode);
    }

    protected function getApp() {
        return $this->app;
    }
}
