<?php
namespace Hyperframework;

class DataLoader {
    public static function load($defaultPath, $pathConfigName = null) {
        $class = get_called_class();
        $delegate = Config::get($class . '\Delegate');
        if ($delegate !== null) {
            return $delegate::load($defaultPath, $pathConfigName);
        }
        return static::loadByFullPath(
            static::getFullPath($defaultPath, $pathConfigName, $class)
        );
    }

    protected static function getFullPath(
        $defaultPath, $pathConfigName, $class
    ) {
        $config = null;
        if ($pathConfigName !== null) {
            $config = Config::get($pathConfigName);
        }
        if ($config !== null) {
            return $config;
        }
        $rootPath = Config::get($class . '\RootPath');
        if ($rootPath === null) {
            $rootPath = static::getDefaultRootPath();
        }
        return $rootPath . DIRECTORY_SEPARATOR
            . $defaultPath . static::getDefaultFileNameExtension();
    }

    protected static function getDefaultFileNameExtension() {
        return '.php';
    }

    protected static function getDefaultRootPath() {
        return Config::getApplicationPath();
    }

    protected static function loadByFullPath($fullPath) {
        return require $fullPath;
    }
}
