<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class ViewDispatcher {
    private static $defaultClasses;

    final public static function dispatch($app) {
        $view = new ViewTemplate($app->getActionResult());
        $name = '';
        $router = $app->getRouter();
        if ($router->getModule()) {
            $name .= $module;
        }
        if ($router->getController()) {
            $name .= $router->getController() . '/';
        }
        if ($router->getAction()) {
            $name .= $router->getAction();
        }
        if ($router->hasParam('format')) {
            $name .= '.' . $router->getParam('format');
        }
        $name .= '.php';
        $view->render($name);
        return;

        $class = self::getClass($pathInfo);
        if ($class === null) {
            throw new NotAcceptableException;
        }
        $view = new $class;
        $view->render($actionResult);
    }

    public static function reset() {
        self::$defaultClasses = null;
    }

    protected static function getNamespace($pathInfo) {
        return $pathInfo['namespace'];
    }

    private static function getClass($pathInfo) {
        if (isset($pathInfo['views']) === false) {
            return self::getDefaultClass();
        }
        $views = $pathInfo['views'];
        $class = null;
        if (empty($_SERVER['REQUEST_MEDIA_TYPE'])) {
            $class = reset($views);
        } elseif (isset($views[$_SERVER['REQUEST_MEDIA_TYPE']])) {
            $class = $views[$_SERVER['REQUEST_MEDIA_TYPE']];
        } else {
            return self::getDefaultClass();
        }
        return static::getNamespace($pathInfo) . '\\' . $class;
    }

    private static function getDefaultClass() {
        if (isset($_SERVER['REQUEST_MEDIA_TYPE'])
            && isset(self::$defaultClasses[$_SERVER['REQUEST_MEDIA_TYPE']])
        ) {
            return self::$defaultClasses[$_SERVER['REQUEST_MEDIA_TYPE']];
        }
    }
}
