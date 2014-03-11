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

    public static function enableView() {
        self::$isViewEnabled = true;
    }

    public static function disableView() {
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

    public static function getMediaType() {
        return self::$mediaType;
    }
 
    public static function redirect($url, $statusCode = 301) {
        self::$isViewEnabled = false;
        header('Location: ' . $url, true, $statusCode);
    }

    public static function reset() {
        self::$actionResult = null;
        self::$mediaType = null;
        self::$pathInfo = null;
        self::$isViewEnabled = true;
    }

    protected static function initialize($path) {
        static::initializePathInfo($path);
        static::initializeMediaType();
    }

    protected static function initializeMediaType() {
        if (isset($_SERVER['REQUEST_MEDIA_TYPE'])) {
            self::$mediaType = $_SERVER['REQUEST_MEDIA_TYPE'];
            return;
        }
        if (isset(self::$pathInfo['views']) === false) {
            self::$mediaType = null;
            return;
        }
        $views = self::$pathInfo['views'];
        if (is_string($views)) {
            self::$mediaType = $views;
            return;
        }
        self::$mediaType = $views[0];
    }

    protected static function initializePathInfo($path) {
        self::$pathInfo = PathInfo::get($path);
    }

    protected static function setPathInfo($value) {
        self:$pathInfo = $value; 
    }

    protected static function setMediaType($value) {
        self::$mediaType = $value; 
    }

    protected static function executeAction() {
        self::$actionResult = ActionDispatcher::run(self::$pathInfo);
    }

    protected static function renderView() {
        if (self::$isViewEnabled) {
            ViewDispatcher::run(self::$pathInfo, self::$mediaType);
        }
    }

    protected static function setActionResult($value) {
        self::$actionResult = $value;
    }

    protected static function isViewEnabled() {
        return self::$isViewEnabled;
    }
}
