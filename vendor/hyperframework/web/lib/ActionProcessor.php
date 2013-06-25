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
        $methods = $info['methods'];
        if (isset($methods[$method])) {
            $action = new $info['class'];
            return $action->$method();
        }
        if (isset($info['get_not_allowed'])) {
            $this->throwMethodNotAllowedException($methods);
        }
        if ($method !== 'GET') {
            $methods['GET'] = 1;
            $methods['HEAD'] = 1;
            $this->throwMethodNotAllowedException($methods);
        }
    }

    private function checkImplicitAction($method) {
        if ($method !== 'GET') {
            $this->throwMethodNotAllowedException(array('GET', 'HEAD'));
        }
    }

    private function throwMethodNotAllowedException($methods) {
        throw new MethodNotAllowedException(
            implode(', ', array_keys($methods))
        );
    }
}
