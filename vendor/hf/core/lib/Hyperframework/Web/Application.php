<?php
namespace Hyperframework\Web;

class Application {
    private static $actionResult;
    private static $isViewEnabled = true;

    public static function run($path) {
        $pathInfo = static::getPathInfo($path);
        static::$actionResult = static::executeAction($pathInfo);
        static::renderView($pathInfo);
    }

    public static function enableView() {
        static::$isViewEnabled = true;
    }

    public static function disableView() {
        static::$isViewEnabled = false;
    }

    public static function getActionResult($key = null/*, ...*/) {
        if ($key === null) {
            return static::$actionResult;
        }
        $result = static::$actionResult;
        foreach (func_get_args() as $key) {
            if (isset($result[$key]) === false) {
                return;
            }
            $result = $result[$key];
        }
        return $result;
    }

    public static function redirect($url, $statusCode = 301) {
        static::$isViewEnabled = false;
        header('Location: ' . $url, true, $statusCode);
    }

    public static function reset() {
        static::$actionResult = null;
        static::$isViewEnabled = true;
    }

    protected static function getPathInfo($path) {
        return PathInfo::get($path);
    }

    protected static function executeAction($pathInfo) {
        return ActionDispatcher::run($pathInfo);
    }

    protected static function setActionResult($value) {
        static::$actionResult = $value;
    }

    protected static function renderView($pathInfo) {
        if (static::$isViewEnabled && isset($pathInfo['views'])) {
            ViewDispatcher::run($pathInfo);
        }
    }
}
