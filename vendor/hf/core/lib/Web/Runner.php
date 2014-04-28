<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\ClassLoader;

class Runner {
    public static function run(
        $rootNamespace, $rootPath, $isAssetProxyEnabled = false
    ) {
        define('Hyperframework\APPLICATION_ROOT_NAMESPACE', $rootNamespace);
        define('Hyperframework\APPLICATION_ROOT_PATH', $rootPath);
        static::initialize();
        $urlPath = static::getUrlPath();
        if ($isAssetProxyEnabled && static::isAsset($urlPath)) {
            static::runAssetProxy($urlPath);
            return;
        }
        static::runApplication($urlPath);
    }

    protected static function initialize() {
        static::initializeConfig();
        static::initializeClassLoader();
        static::initializeExceptionHandler();
    }

    protected static function getUrlPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if ($result === '') {
            return '/';
        }
        if ($result[0] === '#') {
            throw new NotFoundException;
        }
        return $result;
    }

    protected static function isAsset($urlPath) {
        return strncmp($urlPath, '/asset/', 8) === 0;
    }

    protected static function runAssetProxy($urlPath) {
        AssetProxy::run($urlPath);
    }

    protected static function runApplication($urlPath) {
        $applicationPath = Router::run($urlPath);
        Application::run($applicationPath);
    }

    protected static function initializeConfig() {
        static::loadConfigClass();
        static::importInitConfig();
    }

    protected static function initializeClassLoader() {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        ClassLoader::run();
    }

    protected static function initializeExceptionHandler() {
        ExceptionHandler::run();
    }

    protected static function loadConfigClass() {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Config.php';
    }

    protected static function importInitConfig() {
        $configs = require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
            . DIRECTORY_SEPARATOR . 'init.php';
        if ($configs !== null) {
            Config::import($configs);
        }
    }
}
