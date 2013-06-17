<?php
namespace Hyperframework\Web;

class ActionProcessor {
    public function run($cache) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'HEAD' && isset($cache['method']['HEAD']) === false) {
            $method = 'GET';
        }
        if ($cache === null) {
            $this->checkImplicitAction($method);
            return;
        }
        $methodList = $cache['method'];
        if (isset($methodList[$method])) {
            $action = new $cache['class'];
            $action->$method();
            return;
        }
        if (isset($cache['get_not_allowed'])) {
            $this->throwMethodNotAllowedException($methodList);
        }
        if ($method !== 'GET') {
            $methodList['HEAD'] = 1;
            $methodList['GET'] = 1;
            $this->throwMethodNotAllowedException($methodList);
        }
    }

    private function checkImplicitAction($method) {
        if ($method !== 'GET') {
            $this->throwMethodNotAllowedException(array('HEAD', 'GET'));
        }
    }

    private function throwMethodNotAllowedException($methodList) {
        throw new MethodNotAllowedException(
            implode(', ', array_keys($methodList))
        );
    }
}
