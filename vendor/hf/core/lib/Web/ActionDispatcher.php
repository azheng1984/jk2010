<?php
namespace Hyperframework\Web;

class ActionDispatcher {
    public static function run($pathInfo, $app) {
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
        if (isset($info['methods']) === false
            || in_array($method, $info['methods']) === false
        ) {
            self::checkImplicitMethod($method, $info);
        }
        $hasBeforeFilter = isset($info['before_filter']);
        $hasAfterFilter = isset($info['after_filter']);
        if ($hasMethod === false
            && $hasBeforeFilter === false
            && $hasAfterFilter === false
        ) {
            return;
        }
        $result = null;
        $class = $pathInfo['namespace'] . '\Action';
        $action = new $class($app);
        if ($hasBeforeFilter) {
            $action->before($app);
        }
        if ($hasMethod) {
            $result = $action->$method($app);
        }
        if ($hasAfterFilter) {
            $action->after($app);
        }
        return $result;
    }

    private static function checkImplicitAction($method) {
        if ($method !== 'GET') {
            throw new HttpMethodNotAllowedException(array('HEAD', 'GET'));
        }
    }

    private static function checkImplicitMethod($method, $info) {
        if (isset($info['get_not_allowed'])) {
            $methods = isset($info['methods']) ? $info['methods'] : array();
            throw new MethodNotAllowedException($methods);
        }
        if ($method === 'GET') {
            return;
        }
        $methods = isset($info['methods']) ? $info['methods'] : array();
        $methods[] = 'HEAD';
        if (in_array('GET', $methods) === false) {
            $methods[] = 'GET';
        }
        throw new MethodNotAllowedException($methods);
    }
}
