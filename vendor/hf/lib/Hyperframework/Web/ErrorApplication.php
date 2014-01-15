<?php
namespace Hyperframework\Web;

class ErrorApplication {
    public static function run($statusCode) {
        static::initialize();
        $path = static::getErrorPath($statusCode);
        echo $path;
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
