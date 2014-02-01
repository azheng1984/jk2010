<?php
namespace Hyperframework\Web;

class ErrorApplication {
    public static function run($statusCode) {
        $path = static::getPath($statusCode);
        $pathInfo = PathInfo::get($path);
        try {
            ViewDispatcher::run($pathInfo);
        } catch (UnsupportedMediaTypeException $ignoredException) {}
    }

    protected static function getPath($statusCode) {
        if (strncmp($statusCode, '4', 1) === 0) {
            return '#Error/Client';
        }
        return '#Error/Server';
    }
}
