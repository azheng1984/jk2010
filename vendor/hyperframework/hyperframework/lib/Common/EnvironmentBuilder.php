<?php
namespace Hyperframework\Common;

use Exception;
use Hyperframework;
 
class EnvironmentBuilder {
    public static function build($appRootPath) {
        static::initializeConfig($appRootPath);
        static::initializeClassLoader();
    }

    protected static function initializeConfig($appRootPath) {
        if (class_exists('Hyperframework\Common\Config') === false) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';
        }
        if (Config::get('hyperframework.app_root_path') === null) {
            if ($appRootPath === null) {
                throw new Exception;
            }
            Config::set('hyperframework.app_root_path', $appRootPath);
        }
        $configs = require Config::get('hyperframework.app_root_path')
            . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'init.php';
        if ($configs !== null) {
            Config::import($configs);
        }
    }

    protected static function initializeClassloader() {
        $composerAutoloadFilePath = Config::get(
            'hyperframework.composer_autoload_file_path'
        );
        if ($composerAutoloadFilePath === null) {
            $composerAutoloadFilePath =
                Config::get('hyperframework.app_root_path')
                    . DIRECTORY_SEPARATOR . 'vendor'
                    . DIRECTORY_SEPARATOR . 'autoload.php';
        } else {
            $isFull = FullPathRecognizer::isFull($composerAutoloadFilePath);
            if ($isFull === false) {
                $composerAutoloadFilePath =
                    Config::get('hyperframework.app_root_path')
                        . DIRECTORY_SEPARATOR . $composerAutoloadFilePath;
            }
        }
        require $composerAutoloadFilePath;
    }
}
