<?php
namespace Hyperframework;

class DataLoader {
    public static function load($pathConfigName, $defaultPath) {
        $class = get_called_class();
        $provider = Config::get($class . '\Provider');
        if ($provider !== null) {
            $path = Config::get(
                $pathConfigName, array('default' => $defaultPath)
            );
            return $provider::get($path);
        }
        $path = Config::get($pathConfigName);
        if ($path === null) {
            $path = static::getRootPath($class)
                . DIRECTORY_SEPARATOR . $defaultPath
                . static::getDefaultFileNameExtension();
        }
        static::loadContent($path);
    }

    protected static function getRootPath($class) {
        $result = Config::get($class . '\RootPath');
        if ($result !== null) {
            return $result;
        }
    }

    protected static function getDefaultFileNameExtension() {
        return '.php';
    }

    protected static function getDefaultRootPath() {
        $appPath = Config::getAppPath();
        if ($appPath === null) {
            throw new Exception;
        }
        return $appPath . DIRECTORY_SEPARATOR
            . static::getDefaultRelativePathByApp();
    }

    protected static function getDefaultRelativePathByApp() {
        return 'data';
    }

    protected static function loadContent($path) {
        return require $path;
    }
}
