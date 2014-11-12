<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class ViewHandler {
    public static function handle($app) {
        $view = $app->getView();
        if (is_object($view)) {
            if (method_exsits($view, 'render')) {
                $view->render();
                return;
            } else {
                throw new Exception;
            }
        }
        if ($view === null) {
            $router = $app->getRouter();
            if ($router->getModule() !== null) {
                $view .= $module;
            }
            $view .= $router->getController() . '/' . $router->getAction();
            if ($router->hasParam('format')) {
                $view .= '.' . $router->getParam('format');
            }
            $view .= '.php';
        } elseif (is_string($view) === false) {
            throw new Exception;
        }
        (new ViewTemplate($app->getActionResult()))->render($view);
    }
}
