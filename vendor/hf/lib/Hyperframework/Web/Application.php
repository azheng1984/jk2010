<?php
namespace Hyperframework\Web;

class Application {
    private static $isViewEnabled = true;

    public static function run($path) {
        $pathInfo = static::getPathInfo($path);
        static::executeAction($pathInfo);
        var_dump($pathInfo);
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

    protected static function getPathInfo($path) {
        return PathInfo::get($path);
    }

    protected static function executeAction($pathInfo) {
        ActionDispatcher::run($pathInfo);
    }

    protected static function renderView($pathInfo) {
        if (static::$isViewEnabled && isset($pathInfo['views'])) {
            ViewDispatcher::run($pathInfo);
        }
    }
}
