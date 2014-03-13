<?php
namespace Hyperframework\Web;

class Application {
    private static $actionResult;
    private static $pathInfo;
    private static $mediaType;
    private static $isViewEnabled = true;

    public static function run($path) {
        static::initialize($path);
        static::executeAction();
        static::renderView();
    }

    final public static function enableView() {
        self::$isViewEnabled = true;
    }

    final public static function disableView() {
        self::$isViewEnabled = false;
    }

    public static function getActionResult($key = null/*, ...*/) {
        if ($key === null) {
            return self::$actionResult;
        }
        $result = self::$actionResult;
        foreach (func_get_args() as $key) {
            if (isset($result[$key]) === false) {
                return;
            }
            $result = $result[$key];
        }
        return $result;
    }

    public static function redirect($url, $statusCode = 301) {
        self::$isViewEnabled = false;
        header('Location: ' . $url, true, $statusCode);
    }

    public static function reset() {
        self::$actionResult = null;
        self::$pathInfo = null;
        self::$mediaType = null;
        self::$isViewEnabled = true;
    }

    protected static function initialize($path) {
        static::initializePathInfo($path);
        static::initializeMediaType();
    }

    protected static function executeAction() {
        self::$actionResult = ActionDispatcher::run(self::$pathInfo);
    }

    protected static function renderView() {
        if (self::$isViewEnabled) {
            ViewDispatcher::run(self::$pathInfo, self::$mediaType);
        }
    }

    protected static function initailizePathInfo($path) {
        self::$pathInfo = PathInfo::get($path);
    }

    protected static function initializeMediaType() {
        if (isset($_SERVER['REQUEST_MEDIA_TYPE'])) {
            self::$mediaType = $_SERVER['REQUEST_MEDIA_TYPE'];
            return;
        }
        $pathInfo = static::getPathInfo();
        if (isset($pathInfo['views']) === false) {
            self::$mediaType = null;
            return;
        }
        $views = $pathInfo['views'];
        if (is_string($views)) {
            self::$mediaType = $views;
            return;
        }
        self::$mediaType = $views[0];
    }

    final protected static function getPathInfo() {
        return self:$pathInfo;
    }

    final protected static function getMediaType() {
        return self::$mediaType;
    }

    final protected static function setMediaType($value) {
        self::$mediaType = $value;
    }

    final protected static function setActionResult($value) {
        self::$actionResult = $value;
    }

    final protected static function isViewEnabled() {
        return self::$isViewEnabled;
    }
}
