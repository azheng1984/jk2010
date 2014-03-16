<?php
namespace Hyperframework\Web;

class ErrorApplication {
    private static $statusCode;

    final public static function run($statusCode) {
        self::$statusCode = $statusCode;
        try {
            static::renderView(static::getPath());
        } catch (NotAcceptableException $ignoredException) {}
    }

    final public static function getStatusCode() {
        return self::$statusCode;
    }

    protected static function renderView($path) {
        $pathInfo = PathInfo::get($path);
        ViewDispatcher::run(MediaTypeSelector::select($pathInfo), $pathInfo);
    }

    protected static function getPath() {
        return '#Error';
    }
}
