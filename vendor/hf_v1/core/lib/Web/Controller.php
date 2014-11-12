<?php
namespace Hyperframework\Web;

class Controller implements ArrayAccess {
    private $app;
    private $filters;

    public function __construct($app) {
        $this->app = $app;
    }

    public function addBeforeFilter($callback, array $options = null) {
        $this->addBeforeFilter('beginTransaction', []);
        $this->addAfterFilter('commitTransaction', []);
        $this->addAfterThrowingFilter('rollbackTransaction', []);
    }

    public function addAfterFilter($callback, array $options = null) {
    }

    public function addAfterThrowingFilter(
        $callback, array $options = null
    ) {
    }

    public function addAroundFilter($callback, array $options = null) {
    }

    public function getApp() {
        return $this->app;
    }

    public function getFilters() {
        return $this->filters;
    }

    protected function getRouteParam($name) {
        $this->app->getRouter()->getParam($name);
    }

    protected function getRouteParams() {
        $this->app->getRouter()->getParams();
    }

    protected function setRouteParam($name, $value) {
        $this->app->getRouter()->setParam($name, $value);
    }

    protected function removeRouteParam($name) {
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
}
