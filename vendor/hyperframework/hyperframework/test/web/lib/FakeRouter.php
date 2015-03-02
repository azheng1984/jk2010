<?php
namespace Hyperframework\Web\Test;

class FakeRouter {
    private $params = [];
    private $module;
    private $moduleNamespace;
    private $controller;
    private $controllerClass;
    private $action;
    private $actionMethod;

    public function getAction() {
        return $this->action;
    }
 
    public function setAction($value) {
        $this->action = $value;
    }

    public function getActionMethod() {
        return $this->actionMethod;
    }
 
    public function setActionMethod($value) {
        $this->actionMethod = $value;
    }

    public function getController() {
        return $this->controller;
    }

    public function setController($value) {
        $this->controller = $value;
    }

    public function getControllerClass() {
        return $this->controllerClass;
    }

    public function setControllerClass($value) {
        $this->controllerClass = $value;
    }

    public function getModule() {
        return $this->module;
    }

    public function setModule($value) {
        $this->module = $value;
    }

    public function getModuleNamespace() {
        return $this->moduleNamespace;
    }

    public function setModuleNamespace($value) {
        $this->moduleNamespace = $value;
    }

    public function getParam($name) {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
    }

    public function getParams() {
        return $this->params;
    }

    public function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    public function removeParam($name) {
        unset($this->params[$name]);
    }

    public function hasParam($name) {
        return isset($this->params[$name]);
    }
}
