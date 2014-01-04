<?php
namespace Hyperframework\Web;

class Application {
    private static $actionResult;
    private static $isViewEnabled = true;

    public static function run($path = null) {
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
        static::$isViewDisabled = true;
        header('Location: ' . $url, true, $statusCode);
    }

    public static function reset() {
        static::$actionResult = null;
        static::$isViewEnabled = true;
    }

    protected static function executeAction(
        $pathInfo, $dispatcherClass = 'Hyperframework\Web\ActionDispatcher'
    ) {
        $dispatcher = new $dispatcherClass;
        static::$actionResult = $dispatcher->run($pathInfo);
    }

    protected static function renderView(
        $pathInfo, $dispatcherClass = 'Hyperframework\Web\ViewDispatcher'
    ) {
        if (static::$isViewEnabled
            && isset($pathInfo['view'])
            && $_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            $dispatcher = new $dispatcherClass;
            $dispatcher->run($pathInfo);
        }
    }

    protected static function getActionResult() {
        return static::$actionResult;
    }

    protected static function setActionResult($value) {
        static::$actionResult = $value;
    }
}
