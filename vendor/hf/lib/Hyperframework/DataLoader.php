<?php
namespace Hyperframework;

class DataLoader {
    public static function load(
        $path, $pathConfigName = null, $isRelativePath = true
    ) {
        $class = get_called_class();
        $delegate = Config::get($class . '\Delegate');
        if ($delegate !== null) {
            return $delegate::load($path, $pathConfigName, $isRelativePath);
        }
        $fullPath = static::getFullPath(
            $path, $pathConfigName, $isRelativePath, $class
        );
        return static::loadByFullPath($fullPath);
    }

    protected static function getFullPath(
        $path, $pathConfigName, $isRelativePath, $class
    ) {
        $config = null;
        if ($pathConfigName !== null) {
            $config = Config::get($pathConfigName);
        }
        if ($config !== null) {
            return $config;
        }
        if ($isRelativePath) {
            $path = static::getRootPath($class) . DIRECTORY_SEPARATOR . $path;
        }
        $extension = static::getFileNameExtension();
        if ($extension !== null) {
            $path .= $extension;
        }
        return $path;
    }

    protected static function getDefaultRootPath() {
        Config::getApplicationPath();
    }

    protected static function getFileNameExtension() {
        return '.php';
    }

    protected static function loadByFullPath($fullPath) {
        return require $fullPath;
    }

    private static function getRootPath($class) {
        $result = Config::get($class . '\RootPath');
        if ($result !== null) {
            return $result;
        }
        return static::getDefaultRootPath();
    }
}
