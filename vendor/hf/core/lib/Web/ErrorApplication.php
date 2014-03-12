<?php
namespace Hyperframework\Web;

class ErrorApplication {
    private static $statusCode;

    public static function run($statusCode) {
        self::$statusCode = $statusCode;
        $path = static::getPath($statusCode);
        $pathInfo = PathInfo::get($path);
        try {
            ViewDispatcher::run($pathInfo);
        } catch (UnsupportedMediaTypeException $ignoredException) {}
    }

    public static function getStatusCode() {
        return self::$statusCode;
    }

    protected static function getPath($statusCode) {
        if (strncmp($statusCode, '4', 1) === 0) {
            return '#Error/Client';
        }
        return '#Error/Server';
    }
}
