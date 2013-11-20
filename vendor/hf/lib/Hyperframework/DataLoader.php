<?php
namespace Hyperframework;

abstract class DataLoader {
    abstract protected static function getDefaultRootPath();

    protected static function load($type, $pathConfigName, $defaultPath) {
        $provider = Config::get(get_called_class() . '\Provider');
        if ($provider !== null) {
            $path = Config::get(
                $pathConfigName, array('default' => $defaultPath)
            );
            return $provider::get($path);
        }
        $path = Config::get($pathConfigName);
        if ($path === null) {
            $path = static::getRootPath() .
                DIRECTORY_SEPARATOR . $defaultPath . '.' . $type . '.php';
        }
        return require $path;
    }

    private static function getRootPath() {
        $result = Config::get(get_called_class() . '\RootPath');
        if ($result === null) {
             return static::getDefaultRootPath();
        }
        return $result;
    }
}
