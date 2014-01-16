<?php
namespace Hyperframework\Web;

class ErrorRunner {
    public static function run($statusCode) {
        $path = static::getPath($statusCode);
        if ($path !== null) {
            static::initailize();
            static::runApp($path);
        }
    }

    protected static function getPath($statusCode) {
        if (strncmp($statusCode, '4', 1) === 0) {
            return '#Error/Client';
        }
        return '#Error/Server';
    }

    protected static function initialize() {
        $_SERVER['PREVIOUS_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    protected static function runApp($path) {
        Application::reset();
        try {
            Application::run($path);
        } catch (Exeptions\UnsupportedMediaTypeException $ignoredException) {}
    }
}
