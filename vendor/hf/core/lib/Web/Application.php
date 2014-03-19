<?php
namespace Hyperframework\Web;

class Application {
    private static $actionResult;
    private static $pathInfo;
    private static $mediaType;
    private static $isViewEnabled = true;
    private static $shouldRewriteRequestMethod = true;

    public static function run($path) {
        static::initialize($path);
        static::executeAction();
        static::renderView();
    }

    final public static function getActionResult($key = null/*, ...*/) {
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

    final public static function disableView() {
        self::$isViewEnabled = false;
    }

    final public static function disableRequestMethodRewriting() {
        self::$shouldRewriteRequestMethod = false;
    }

    public static function reset() {
        self::$actionResult = null;
        self::$pathInfo = null;
        self::$mediaType = null;
        self::$isViewEnabled = true;
        self::$shouldRewriteRequestMethod = true;
    }

    protected static function initialize($path) {
        static::initializePathInfo($path);
        static::initializeMediaType();
        static::rewriteRequestMethod();
    }

    protected static function executeAction() {
        self::$actionResult = ActionDispatcher::run(self::$pathInfo);
    }

    protected static function renderView() {
        if (self::$isViewEnabled) {
            ViewDispatcher::run(self::$mediaType, self::$pathInfo);
        }
    }

    protected static function initailizePathInfo($path) {
        self::$pathInfo = PathInfo::get($path);
    }

    protected static function initializeMediaType() {
        self::$mediaType = MediaTypeSelector::select(self::$pathInfo);
    }

    protected static function rewriteRequestMethod() {
        if (self::$shouldRewriteRequestMethod
            && $_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['_method'])) {
            $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
        }
    }

    final protected static function getPathInfo() {
        return self:$pathInfo;
    }

    final protected static function setPathInfo($value) {
        self:$pathInfo = $value;
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

    final protected static function shouldRewriteRequestMethod() {
        return self::$shouldRewriteRequestMethod;
    }
}
