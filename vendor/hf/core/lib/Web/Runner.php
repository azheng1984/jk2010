<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\ClassLoader;

class Runner {
    public static function run(
        $applicationNamespace, $applicationPath, $configs = null
    ) {
        static::initialize($applicationNamespace, $applicationPath, $configs);
        $path = static::getPath();
        if (static::isAsset($path)) {
            static::runAssetProxy($path);
            return;
        }
        static::runApplication($path);
    }

    protected static function initialize($applicationNamespace, $configs) {
        //static::initailizeApplicationPath($applicationPath);
        define('Hyperframework\APPLICATION_PATH', $applicationPath);
        static::initializeConfig($applicationNamespace, $configs);
        static::initializeClassLoader();
        static::initializeExceptionHandler();
    }

    protected static function initializeConfig(
        $applicationNamespace, $configs
    ) {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Config.php';
        Config::initialize($applicationNamespace);
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
