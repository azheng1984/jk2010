<?php
namespace Hyperframework\Common;

use Hyperframework;
 
class EnvironmentBuilder {
    public static function build($appRootNamespace, $appRootPath) {
        self::initializeAppRootPathConstant($appRootPath);
        self::initializeAppRootNamespaceConstant($appRootNamespace);
        static::initializeConfig();
        static::initializeClassLoader();
    }

    protected static function initializeConfig() {
        if (class_exists('Hyperframework\Common\Config') === false) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';
        }
        $configs = require Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR . 'init.php';
        if ($configs !== null) {
            Config::import($configs);
        }
    }

    protected static function initializeClassloader() {
        $composerAutoloadFilePath = Config::get(
            'hyperframework.composer_autoload_file_path'
        );
        if ($composerAutoloadFilePath === null) {
            $composerAutoloadFilePath = Hyperframework\APP_ROOT_PATH
                . DIRECTORY_SEPARATOR . 'vendor'
                . DIRECTORY_SEPARATOR . 'autoload.php';
        } else {
            $isFull = FullPathRecognizer::isFull($composerAutoloadFilePath);
            if ($isFull === false) {
                $composerAutoloadFilePath = Hyperframework\APP_ROOT_PATH
                    . DIRECTORY_SEPARATOR . $composerAutoloadFilePath;
            }
        }
        require $composerAutoloadFilePath;
    }

    private static function initializeAppRootPathConstant($appRootPath) {
        if (defined('Hyperframework\APP_ROOT_PATH') === false) {
            if ($appRootPath === null) {
                throw new Exception;
            }
            define('Hyperframework\APP_ROOT_PATH', $appRootPath);
        }
    }

    private static function initializeAppRootNamespaceConstant(
        $appRootNamespace
    ) {
        if (defined('Hyperframework\APP_ROOT_NAMESPACE') === false) {
            define('Hyperframework\APP_ROOT_NAMESPACE', $appRootNamespace);
        }
    }
}
