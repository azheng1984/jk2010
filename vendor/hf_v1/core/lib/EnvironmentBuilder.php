<?php
namespace Hyperframework;
 
class EnvironmentBuilder {
    public static function build($appRootNamespace, $appRootPath) {
        define('Hyperframework\APP_ROOT_NAMESPACE', $appRootNamespace);
        define('Hyperframework\APP_ROOT_PATH', $appRootPath);
        static::initializeConfig();
        static::initializeAutoloader();
    }

    protected static function initializeConfig() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';
        $config = require APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR . 'init.php';
        if ($config !== null) {
            Config::import($config);
        }
    }

    protected static function initializeAutoloader() {
        $autoloadFilePath = Config::get(
            'hyperframework.composer_autoload_file_path'
        );
        if ($autoloadFilePath === null) {
            $autoloadFilePath = APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
                . DIRECTORY_SEPARATOR . 'autoload.php';
        } elseif (FullPathRecognizer::isFull($autoloadFilePath) === false) {
            $autoloadFilePath = APP_ROOT_PATH . DIRECTORY_SEPARATOR
                . $autoloadFilePath;
        }
        require $autoloadFilePath;
    }
}
