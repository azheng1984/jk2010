<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

final class ActionDispatcher {
    public static function run($pathInfo, $ctx) {
        $actionInfo = null;
        if (isset($pathInfo['action'])) {
            $actionInfo = $pathInfo['action'];
        }
        $method = static::getMethod($actionInfo);
        $hasBeforeFilter = isset($actionInfo['before_filter']);
        $hasAfterFilter = isset($actionInfo['after_filter']);
        if ($method === null
            && $hasBeforeFilter === false
            && $hasAfterFilter === false
        ) {
            return;
        }
        $result = null;
        $class = static::getClass($pathInfo);
        $action = new $class($ctx);
        if ($hasBeforeFilter) {
            $action->before($ctx);
        }
        if ($method !== null) {
            $result = $action->$method($ctx);
        }
        if ($hasAfterFilter) {
            $action->after($ctx);
        }
        return $result;
    }

    protected static function getMethod($actionInfo) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        if ($actionInfo === null) {
            if ($method !== 'GET') {
                throw new HttpMethodNotAllowedException(array('HEAD', 'GET'));
            }
            return;
        }
        if (isset($actionInfo['methods'])
            && in_array($method, $actionInfo['methods'])
        ) {
            return strtolower($method);
        }
        if (isset($actionInfo['get_not_allowed'])) {
            $methods = isset($actionInfo['methods']) ?
                $actionInfo['methods'] : array();
            throw new MethodNotAllowedException($methods);
        }
        if ($method === 'GET') {
            return;
        }
        $methods = isset($actionInfo['methods']) ?
            $actionInfo['methods'] : array();
        $methods[] = 'HEAD';
        if (in_array('GET', $methods) === false) {
            $methods[] = 'GET';
        }
        throw new MethodNotAllowedException($methods);
    }

    protected static function getClass($pathInfo) {
        return $pathInfo['namespace'] . '\Action';
    }
}
