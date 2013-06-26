<?php
namespace Hyperframework\Web;

class ActionProcessor {
    public function run($info) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        if ($info === null) {
            $this->checkImplicitAction($method);
            return;
        }
        $isExplicitMethod = isset($info['methods'][$method]);
        if ($isExplicitMethod === false) {
            $this->checkImplicitMethod($info, $method);
        }
        $hasBeforeFilter = isset($info['before_filter']);
        $hasAfterFilter = isset($info['after_filter']);
        if ($isExplicitMethod === false && $hasBeforeFilter === false &&
            $hasAfterFilter === false) {
            return;
        }
        $action = new $info['class'];
        if ($hasBeforeFilter) {
            $action->before();
        }
        if ($isExplicitMethod) {
            $result = $action->$method();
        }
        if ($hasAfterFilter) {
            $action->after();
        }
        return $result;
    }

    private function checkImplicitAction($method) {
        if ($method !== 'GET') {
            throw new MethodNotAllowedException(array('GET', 'HEAD'));
        }
    }

    private function checkImplicitMethod($info, $method) {
        if (isset($info['get_not_allowed'])) {
            throw new MethodNotAllowedException(array_keys($info['methods']);
        }
        if ($method !== 'GET') {
            $methods = $info['methods'];
            $methods['GET'] = 1;
            $methods['HEAD'] = 1;
            throw new MethodNotAllowedException(array_keys($methods));
        }
    }
}
