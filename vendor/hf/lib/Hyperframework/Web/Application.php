<?php
namespace Hyperframework\Web;

class Application {
    private static $isViewEnabled = true;

    public static function run($path) {
        $pathInfo = PathInfo::get($path);
        static::executeAction($pathInfo);
        static::renderView($pathInfo);
    }

    public static function enableView() {
        static::$isViewEnabled = true;
    }

    public static function disableView() {
        static::$isViewEnabled = false;
    }

    public static function redirect($url, $statusCode = 301) {
        static::$isViewEnabled = false;
        header('Location: ' . $url, true, $statusCode);
    }


    public static function reset() {
        static::$isViewEnabled = true;
    }

    protected static function executeAction(
        $pathInfo, $dispatcherClass = 'Hyperframework\Web\ActionDispatcher'
    ) {
        $dispatcher = new $dispatcherClass;
        $dispatcher->run($pathInfo);
    }

    protected static function renderView(
        $pathInfo, $dispatcherClass = 'Hyperframework\Web\ViewDispatcher'
    ) {
        if (static::$isViewEnabled && isset($pathInfo['views'])) {
            $dispatcher = new $dispatcherClass;
            $dispatcher->run($pathInfo);
        }
    }
}
