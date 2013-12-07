<?php
namespace Hyperframework\Web;

use Hyperframework\Web\Exceptions\MethodNotAllowedException;

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
        $hasMethod = isset($info['methods'][$method]);
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
        $class = $info['namespace'] . $info['class'];
        $action = new $class;
        $result = null;
        if ($hasBeforeFilter) {
            $action->before();
        }
        if ($hasMethod) {
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
            $methods = isset($info['methods']) ?
                array_keys($info['methods']) : array();
            throw new MethodNotAllowedException($methods);
        }
        if ($method !== 'GET') {
            $methods = isset($info['methods']) ? $info['methods'] : array();
            $methods['GET'] = 1;
            $methods['HEAD'] = 1;
            throw new MethodNotAllowedException(array_keys($methods));
        }
    }
}
