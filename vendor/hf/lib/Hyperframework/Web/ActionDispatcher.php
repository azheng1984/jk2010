<?php
namespace Hyperframework\Web;

use Hyperframework\Web\Exceptions\MethodNotAllowedException;

class ActionDispatcher {
    public function run($pathInfo) {
        $info = null;
        if (isset($pathInfo['action'])) {
            $info = $pathInfo['action'];
        }
        $method = $this->getMethod();
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        if ($info === null) {
            $this->checkImplicitAction($method);
            return;
        }
        $hasMethod = in_array($method, $info['methods']);
        if ($hasMethod === false) {
            $this->checkImplicitMethod($info, $method);
        }
        $hasBeforeFilter = isset($info['before_filter']);
        $hasAfterFilter = isset($info['after_filter']);
        if ($hasMethod === false
            && $hasBeforeFilter === false
            && $hasAfterFilter === false) {
            return;
        }
        $class = $pathInfo['namespace'] . '\Action';
        $action = new $class;
        if ($hasBeforeFilter) {
            $action->before();
        }
        if ($hasMethod) {
            $action->$method();
        }
        if ($hasAfterFilter) {
            $action->after();
        }
    }

    protected function getMethod() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
            return $_POST['_method'];
        }
        return $_SERVER['REQUEST_METHOD'];
    }

    private function checkImplicitAction($method) {
        if ($method !== 'GET') {
            throw new MethodNotAllowedException(array('GET', 'HEAD'));
        }
    }

    private function checkImplicitMethod($info, $method) {
        if (isset($info['get_not_allowed'])) {
            $methods = isset($info['methods']) ? $info['methods'] : array();
            throw new MethodNotAllowedException($methods);
        }
        if ($method === 'GET') {
            return;
        }
        $methods = isset($info['methods']) ? $info['methods'] : array();
        if (in_array('GET', $methods) === false) {
            $methods[] = 'HEAD';
            $methods[] = 'GET';
        }
        throw new MethodNotAllowedException($methods);
    }
}
