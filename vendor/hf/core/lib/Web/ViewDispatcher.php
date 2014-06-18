<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public static function run($pathInfo, $ctx) {
        $class = static::getViewClass($pathInfo);
        if ($class === null) {
            throw new NotAcceptableException;
        }
        static::dispatch($class, $ctx);
    }

    protected static function dispatch($class, $ctx) {
        $view = new $class($ctx);
        $view->render($ctx);
    }

    protected static function getViewClass($pathInfo) {
        if (isset($pathInfo['views']) === false) {
            return;
        }
        $class = null;
        if ($_SERVER['REQUEST_MEDIA_TYPE'] === null) {
            $class = reset($views[0]);
        } elseif (isset($views[$_SERVER['REQUEST_MEDIA_TYPE']]) {
            $class = $views[$_SERVER['REQUEST_MEDIA_TYPE']];
        }
        return $pathInfo['namespace'] . '\\' . $class;
    }
}
