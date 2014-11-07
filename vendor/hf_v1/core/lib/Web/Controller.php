<?php
namespace Hyperframework\Web;

class Controller implements ArrayAccess {
    private $app;

    public function __construct($app) {
        $this->app = $app;
        $this['article'] = Article::getById($this->getRouteParam['id']);
        if ($this['article'] !== null) {
        }
    }

    protected static function getRouteParam($name) {
        $this->app->getRouter()->getParam($name);
    }

    protected static function getRouteParams() {
        $this->app->getRouter()->getParams();
    }

    protected static function setRouteParam($name, $value) {
        $this->app->getRouter()->setParam($name, $value);
    }

    protected static function removeRouteParam($name) {
        $this->app->getRouter()->removeParam($name);
    }

    protected function hasRouteParam($name) {
        $this->app->getRouter()->hasParam($name);
    }

    protected function disableView() {
        $this->app->disableView();
    }

    protected function enableView() {
        $this->app->enableView();
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
