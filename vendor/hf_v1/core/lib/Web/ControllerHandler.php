<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class ControllerHandler {
    public function handle($app) {
        $router = $app->getRouter();
        $controllerClass = $router->getControllerClass();
        if ($controllerClass === null
            || class_exists($controllerClass) === false
        ) {
            throw new NotFoundException;
        }
        $controller = new $controllerClass($app);
        $controller->getFilters('before');
        $controller->getFilters('after');
        $controller->getFilters('after_throwing');
        $actionMethod = $router->getActionMethod();
        if ($actionMethod === null) {
            throw new NotFoundException;
        }
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
    }

    public function next() {
    }
}
