<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public static function run($pathInfo) {
        static::initialize($pathInfo);
        static::dispatch($pathInfo);
    }

    protected static function initialize($pathInfo) {
        if (isset($pathInfo['views']) === false) {
            throw new NotAcceptableException;
        }
        $views = $pathInfo['views'];
        if (is_string($views)) {
            $views = array($views);
        }
        if ($_SERVER['REQUEST_MEDIA_TYPE'] === null) {
            $_SERVER['REQUEST_MEDIA_TYPE'] = $views[0];
        } elseif (in_array($_SERVER['REQUEST_MEDIA_TYPE'], $views) === false) {
            throw new NotAcceptableException;
        }
    }

    protected static function dispatch($pathInfo) {
        $class = $pathInfo['namespace'] . '\\' . $_SERVER['REQUEST_MEDIA_TYPE'];
        $view = new $class;
        $view->render();
    }
}
