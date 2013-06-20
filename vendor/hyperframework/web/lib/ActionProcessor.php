<?php
namespace Hyperframework\Web;

class ActionProcessor {
    public function run($info) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'HEAD' && isset($info['method']['HEAD']) === false) {
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
