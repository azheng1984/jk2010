<?php
namespace Hyperframework;

class DataLoader {
    public static function load($defaultPath, $pathConfigName = null) {
        $class = get_called_class();
        $customLoader = Config::getClass($class);
        if ($customLoader !== null) {
            return $customLoader::load($defaultPath, $pathConfigName);
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
        $rootPath = static::getRootPath();
        return $rootPath . DIRECTORY_SEPARATOR . $defaultPath
            . static::getDefaultFileNameExtension();
    }

    protected static function getDefaultFileNameExtension() {
        return '.php';
    }

    protected static function getRootPath() {
        return Config::getApplicationPath() . DIRECTORY_SEPARATOR . 'data';
    }

    protected static function loadByFullPath($fullPath) {
        return require $fullPath;
    }
}
