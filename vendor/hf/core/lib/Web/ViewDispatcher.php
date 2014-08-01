<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    private static $defaultClasses;

    final public static function run($pathInfo, $ctx) {
        $class = self::getClass($pathInfo);
        if ($class === null) {
            throw new NotAcceptableException;
        }
        $view = new $class($ctx);
        $view->render($ctx);
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
