<?php
namespace Hyperframework;

class EnvironmentBuilder {
    public static function run($rootNamespace, $rootPath) {
        define('Hyperframework\APPLICATION_ROOT_NAMESPACE', $rootNamespace);
        define('Hyperframework\APPLICATION_ROOT_PATH', $rootPath);
        static::initializeConfig();
        static::initializeClassLoader();
    }

    protected static function initializeConfig() {
        static::loadConfigClass();
        static::importInitConfig();
    }

    protected static function initializeClassLoader() {
        if (Config::get('hyperframework.use_composer_class_loader') === true) {
            require APPLICATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
                . DIRECTORY_SEPARATOR . 'autoload.php';
            return;
        }
        require __DIR__ . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        ClassLoader::run();
    }

    protected static function loadConfigClass() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';
    }

    protected static function importInitConfig() {
        $config = require APPLICATION_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR . 'init.php';
        if ($config !== null) {
            Config::import($config);
        }
    }
}
