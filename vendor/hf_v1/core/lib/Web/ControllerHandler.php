<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

final class ControllerHandler {
    public static function handle($app) {
        $router = $app->getRouter();
        $controllerClass = $router->getControllerClass();
        if ($controllerClass === null
            || class_exists($controllerClass) === false
        ) {
            throw new NotFoundException;
        }
        $actionMethod = $router->getActionMethod();
        if ($actionMethod === null) {
            throw new NotFoundException;
        }
        $controller = new $controllerClass($app);
        //todo filter
//        $filters = $controller->getFilters('before_action');
        if (method_exists($controller, $actionMethod)) {
            return $controller->$actionMethod();
        }
//        $filters = $controller->getFilters('after_action');
//        if ($filters !== null) {
//            $this->addBeforeActionFilter('callback');
//            foreach ($filters as $filter) {
//                $filter();
//            }
//        }
        return;

        $actionInfo = null;
        if (isset($pathInfo['controller'])) {
            $actionInfo = $pathInfo['controller'];
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
        $controller = new $class($app);
//        if ($hasBeforeFilter) {
//            $controller->initialize();
//        }
        if ($method !== null) {
            $result = $action->$method();
        }
//        if ($hasAfterFilter) {
//            $controller->finalize();
//        }
        return $result;
    }

    protected static function getMethod($actionInfo) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        if ($actionInfo === null) {
            if ($method === 'GET') {
                return;
            }
            throw new MethodNotAllowedException(array('HEAD', 'GET'));
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
