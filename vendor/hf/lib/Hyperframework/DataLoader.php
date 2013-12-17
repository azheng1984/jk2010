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
                . '.' . static::getDefaultFileNameExtension();
        }
        return require $path;
    }

    private static function getRootPath($class) {
        $result = Config::get($class . '\RootPath');
        if ($result !== null) {
            return $result;
        }
        return Config::get(
            __NAMESPACE__ . '\AppPath', array('is_nullable' => false)
        ) . DIRECTORY_SEPARATOR . static::getDefaultRootPath();
    }

    protected static function getDefaultFileNameExtension() {
        return '.php';
    }

    protected static function getDefaultRootPath() {
        return 'data';
    }
}
