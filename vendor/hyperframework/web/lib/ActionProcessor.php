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
        $methodList = $info['method'];
        if (isset($methodList[$method])) {
            $action = new $info['class'];
            return $action->$method();
        }
        if (isset($info['get_not_allowed'])) {
            $this->throwMethodNotAllowedException($methodList);
        }
        if ($method !== 'GET') {
            $methodList['GET'] = 1;
            $methodList['HEAD'] = 1;
            $this->throwMethodNotAllowedException($methodList);
        }
    }

    private function checkImplicitAction($method) {
        if ($method !== 'GET') {
            $this->throwMethodNotAllowedException(array('GET', 'HEAD'));
        }
    }

    private function throwMethodNotAllowedException($methodList) {
        throw new MethodNotAllowedException(
            implode(', ', array_keys($methodList))
        );
    }
}
