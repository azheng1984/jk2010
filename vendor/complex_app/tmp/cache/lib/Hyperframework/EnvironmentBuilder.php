<?php
namespace Hyperframework;
 
class EnvironmentBuilder {
    public static function run($rootNamespace, $rootPath) {
        define('Hyperframework\APP_ROOT_NAMESPACE', $rootNamespace);
        define('Hyperframework\APP_ROOT_PATH', $rootPath);
        static::initializeConfig();
        static::initializeAutoloader();
    }

    protected static function initializeConfig() {
        static::loadConfigClass();
        static::importInitConfig();
    }

    protected static function initializeAutoloader() {
        if (Config::get('hyperframework.use_composer_autoloader') === true) {
            require APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
                . DIRECTORY_SEPARATOR . 'autoload.php';
            return;
        }
        static::loadFiles();
        static::initializeClassLoader();
    }

    protected static function initializeClassLoader() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        ClassLoader::run();
    }

    protected static function loadFiles() {
        if (Config::get('hyperframework.autoload_files.enable') !== true) {
            return;
        }
        $path = Config::get('hyperframework.autoload_files.path');
        if ($path === null) {
            $path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'tmp'
                . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR
                . 'autoload_files.php';
        }
        require $path;
    }

    protected static function loadConfigClass() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';
    }

    protected static function importInitConfig() {
        $config = require APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR . 'init.php';
        if ($config !== null) {
            Config::import($config);
        }
    }
}
