<?php
namespace Hyperframework\Web;

class ErrorApplication {
    private static $statusCode;

    final public static function run($statusCode) {
        self::$statusCode = $statusCode;
        $pathInfo = PathInfo::get(static::getPath($statusCode));
        $mediaType = MediaTypeSelector::select($pathInfo);
        try {
            ViewDispatcher::run($pathInfo, $mediaType);
        } catch (UnsupportedMediaTypeException $ignoredException) {}
    }

    final public static function getStatusCode() {
        return self::$statusCode;
    }

    protected static function getPath($statusCode) {
        if (strncmp($statusCode, '4', 1) === 0) {
            return '#Error/Client';
        }
        return '#Error/Server';
    }
}
