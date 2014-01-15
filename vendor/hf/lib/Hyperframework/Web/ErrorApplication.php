<?php
namespace Hyperframework\Web;

class ErrorApplication {
    private static $previousRequestMethod;

    public static function run($statusCode) {
        $path = static::getErrorPath($statusCode);
        if ($path !== null) {
            static::$previousRequestMethod = $_SERVER['REQUEST_METHOD'];
            $_SERVER['REQUEST_METHOD'] = 'GET';
            Application::reset();
            try {
                Application::run($path);
            } catch (
                Exeptions\UnsupportedMediaTypeException $ignoredException
            ) {}
        }
    }

    public static function getPreviousRequestMethod() {
        return static::$previousRequestMethod;
    }

    public static function reset() {
        static::$previousRequestMethod = null;
    }

    protected static function getErrorPath($statusCode) {
        if (strncmp($statusCode, '4', 1) === 0) {
            return '#Error/Client';
        }
        return '#Error/Server';
    }
}
