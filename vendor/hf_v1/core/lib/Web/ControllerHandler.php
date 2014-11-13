<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class ControllerHandler {
    public static function handle($app) {
        $router = $app->getRouter();
        $controllerClass = $router->getControllerClass();
        if ($controllerClass === null
            || class_exists($controllerClass) === false
        ) {
            throw new NotFoundException;
        }
        $controller = new $controllerClass($app);
        $beforeFilters = [];
        $afterReturningFilters = [];
        $afterThrowingFilters = [];
        $filters = $controller->getFilters();
        foreach ($filters as $filter) {
            switch ($filter['type']) {
                case 'before':
                    break;
                case 'after':
                    break;
                case 'after_throwing':
                    break;
                case 'after_returning':
                    break;
            }
        }
        $actionMethod = $router->getActionMethod();
        if ($actionMethod === null) {
            throw new NotFoundException;
        }
        if (method_exists($controller, $actionMethod)) {
            return $controller->$actionMethod();
        }
    }
}
