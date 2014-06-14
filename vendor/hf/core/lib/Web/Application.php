<?php
namespace Hyperframework\Web;

class Application {
    private static $pathInfo;
    private static $isViewEnabled = true;
    private static $shouldRewriteRequestMethod = true;

    public static function run($path) {
        static::initializePathInfo($path);
        static::executeAction();
        static::renderView();
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
        self::$pathInfo = null;
        self::$isViewEnabled = true;
        self::$shouldRewriteRequestMethod = true;
    }

    protected static function executeAction() {
        self::rewriteRequestMethod();
        ActionResult::initialize(
            ActionDispatcher::run(self::$pathInfo)
        );
    }

    protected static function renderView() {
        if (self::$isViewEnabled) {
            ViewDispatcher::run(self::$pathInfo);
        }
    }

    protected static function initailizePathInfo($path) {
        self::$pathInfo = PathInfo::get($path);
    }

    protected static function rewriteRequestMethod() {
        if (self::$shouldRewriteRequestMethod
            && $_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['_method'])
        ) {
            $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
        }
    }

    final protected static function getPathInfo() {
        return self:$pathInfo;
    }

    final protected static function setPathInfo($value) {
        self:$pathInfo = $value;
    }

    final protected static function isViewEnabled() {
        return self::$isViewEnabled;
    }

    final protected static function shouldRewriteRequestMethod() {
        return self::$shouldRewriteRequestMethod;
    }
}
