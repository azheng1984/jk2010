<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\ClassLoader;

class Runner {
    public static function run($applicationNamespace, $config = array()) {
        static::initialize($applicationNamespace, $config);
        $path = static::getPath();
        if (static::isAsset($path)) {
            static::runAssetProxy($path);
            return;
        }
        static::runApplication($path);
    }

    protected static function initialize($applicationNamespace, $config) {
        static::initializeConfig($applicationNamespace, $config);
        static::initializeClassLoader();
        static::initializeExceptionHandler();
    }

    protected static function initializeConfig($applicationNamespace, $config) {
        require static::getHyperframeworkPath()
            . DIRECTORY_SEPARATOR . 'Config.php';
        Config::initialize($applicationNamespace);
        Config::set($config);
    }

    protected static function initializeClassLoader() {
        require static::getHyperframeworkPath()
            . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        ClassLoader::run();
    }

    protected static function initializeExceptionHandler() {
        ExceptionHandler::run();
    }

    final protected static function getHyperframeworkPath() {
        return dirname(__DIR__);
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
