<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public static function run($pathInfo, $app) {
        if (isset($pathInfo['views']) === false) {
            throw new NotAcceptableException;
        }
        $class = $pathInfo['namespace'] . '\\'
            . static::getClass($pathInfo['views']);
        $view = new $class($app);
        $view->render($app);
    }

    protected static function initialize($views) {
        if ($_SERVER['REQUEST_MEDIA_TYPE'] === null) {
            return reset($views[0]);
        }
        if (isset($views[$_SERVER['REQUEST_MEDIA_TYPE']]) {
            return $views[$_SERVER['REQUEST_MEDIA_TYPE']];
        }
        throw new NotAcceptableException;
    }
}
