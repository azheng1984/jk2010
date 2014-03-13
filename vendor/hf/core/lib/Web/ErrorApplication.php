<?php
namespace Hyperframework\Web;

class ErrorApplication {
    private static $statusCode;

    final public static function run($statusCode) {
        self::$statusCode = $statusCode;
        try {
            static::renderView(static::getPath($statusCode));
        } catch (UnsupportedMediaTypeException $ignoredException) {}       
    }

    final public static function getStatusCode() {
        return self::$statusCode;
    }

    protected static function renderView($path) {
        $pathInfo = PathInfo::get($path);
        ViewDispatcher::run($pathInfo, MediaTypeSelector::select($pathInfo));
    }

    protected static function getPath($statusCode) {
        if (strncmp($statusCode, '4', 1) === 0) {
            return '#Error/Client';
        }
        return '#Error/Server';
    }
}
