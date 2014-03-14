<?php
namespace Hyperframework\Web;

class ActionDispatcher {
    public static function run($pathInfo) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $info = null;
        if (isset($pathInfo['action'])) {
            $info = $pathInfo['action'];
        }
        if ($info === null) {
            self::checkImplicitAction($method);
            return;
        }
        $hasMethod = in_array($method, $info['methods']);
        if ($hasMethod === false) {
            self::checkImplicitMethod($info, $method);
        }
        $hasBeforeFilter = isset($info['before_filter']);
        $hasAfterFilter = isset($info['after_filter']);
        if ($hasMethod === false
            && $hasBeforeFilter === false
            && $hasAfterFilter === false) {
            return;
        }
        $result = null;
        $class = $pathInfo['namespace'] . '\Action';
        $action = new $class;
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

    private static function checkImplicitAction($method) {
        if ($method !== 'GET') {
            throw new MethodNotAllowedException(array('GET', 'HEAD'));
        }
    }

    private static function checkImplicitMethod($info, $method) {
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
