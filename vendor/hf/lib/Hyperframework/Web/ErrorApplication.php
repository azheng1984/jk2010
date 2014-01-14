<?php
namespace Hyperframework\Web;

class ErrorApplication {
    public static final function run($statusCode) {
        static::initailize();
        $path = static::getErrorPath($statusCode);
        if ($path !== null) {
            static::restart($path);
        }
    }

    protected static function initialize() {
        $_SERVER['PREVIOUS_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    protected static function getErrorPath($statusCode) {
        if (strncmp($statusCode, '4', 1) === 0) {
            return '#Error/Client';
        }
        return '#Error/Server';
    }

    protected static function restart($path) {
        Application::reset();
        try {
            Application::run($path);
        } catch (Exeptions\UnsupportedMediaTypeException $recursiveException) {}
    }
}
