<?php
namespace Hyperframework::Web;

class ActionProcessor {
    public function run($cache) {
        $method = $_SERVER['REQUEST_METHOD'];
        if (isset($cache['method'][$method])) {
            $action = new $cache['class'];
            $action->$method();
        }
        if (isset($cache['get_not_allowed'])) {
            $this->throwMethodNotAllowedException($cache['method']);
        }
        if ($method !== 'GET') {
            $methodList = $cache['method'];//测试写时复制是否部分复制
            $methodList['GET'] = 1;
            $this->throwMethodNotAllowedException($methodList);
        }
    }

    private function throwMethodNotAllowedException($methodList) {
        throw new MethodNotAllowedException(
            implode(', ', array_keys($methodList))
        );
    }
}
