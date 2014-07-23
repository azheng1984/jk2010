<?php
namespace Hyperframework\Web;

final class ViewDispatcher {
    private $defaultViewClasses;

    public static function run($pathInfo, $ctx) {
        $class = self::getViewClass($pathInfo);
        if ($class === null) {
            throw new NotAcceptableException;
        }
        $view = new $class($ctx);
        $view->render($ctx);
    }

    public static function setDefaultViewClasses($classes) {
        self::$defaultViewClasses = $classes;
    }

    private static function getViewClass($pathInfo) {
        if (isset($pathInfo['views']) === false) {
            return self::getDefaultViewClass();
        }
        $views = $pathInfo['views'];
        $class = null;
        if (empty($_SERVER['REQUEST_MEDIA_TYPE'])) {
            $class = reset($views);
        } elseif (isset($views[$_SERVER['REQUEST_MEDIA_TYPE']])) {
            $class = $views[$_SERVER['REQUEST_MEDIA_TYPE']];
        } else {
            return self::getDefaultViewClass();
        }
        return $class;
    }

    private static function getDefaultViewClass() {
        if (isset($_SERVER['REQUEST_MEDIA_TYPE'])
            && isset(self::$defaultViewClasses[$_SERVER['REQUEST_MEDIA_TYPE']])
        ) {
            return self::$defaultViewClasses[$_SERVER['REQUEST_MEDIA_TYPE']];
        }
    }
}
