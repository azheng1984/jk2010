<?php
namespace Hyperframework;

abstract class AbstractEnvInitializer {
    public static function run($rootNamespace, $rootPath) {
        define('Hyperframework\APPLICATION_ROOT_NAMESPACE', $rootNamespace);
        define('Hyperframework\APPLICATION_ROOT_PATH', $rootPath);
        static::initializeConfig();
        static::initializeClassLoader();
        static::initializeExceptionHandler();
    }

    protected static function initializeConfig() {
        static::loadConfigClass();
        static::importInitConfig();
    }

    protected static function initializeClassLoader() {
        if (Config::get('hyperframework.use_composer_autoload') === true) {
            require APPLICATION_ROOT_PATH . 'vendor'
                . DIRECTORY_SEPARATOR . 'autoload.php';
            return;
        }
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        ClassLoader::run();
    }

    abstract protected static function initializeExceptionHandler();

    protected static function loadConfigClass() {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Config.php';
    }

    protected static function importInitConfig() {
        $configs = require ROOT_PATH . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR . 'init.php';
        if ($configs !== null) {
            Config::import($configs);
        }
    }
}
