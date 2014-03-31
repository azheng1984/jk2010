<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\ClassLoader;

class Runner {
    public static function run($applicationPath, $configs = null) {
        static::initialize($applicationPath, $configs);
        $path = static::getPath();
        if (static::isAsset($path)) {
            static::runAssetProxy($path);
            return;
        }
        static::runApplication($path);
    }

    protected static function initialize($applicationPath, $configs) {
        static::initailizeApplicationPath($applicationPath);
        static::initializeConfig($configs);
        static::initializeClassLoader();
        static::initializeExceptionHandler();
    }

    protected static function initializeApplicationpath($value) {
        define('Hyperframework\APPLICATION_PATH', $value);
    }

    protected static function initializeConfig($configs) {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Config.php';
        if ($configs !== null) {
            Config::merge($configs);
        }
    }

    protected static function initializeClassLoader() {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        ClassLoader::run();
    }

    protected static function initializeExceptionHandler() {
        ExceptionHandler::run();
    }

    protected static function getPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if (strncmp($result, '#', 1) === 0) {
            throw new NotFoundException;
        }
        return $result;
    }

    protected static function isAsset($path) {
        return strncmp($path, '/asset/', 7) === 0;
    }

    protected static function runAssetProxy() {
        AssetProxy::run($path);
    }

    protected static function runApplication($path) {
        Application::run($path);
    }
}
