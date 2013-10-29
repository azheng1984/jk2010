<?php
namespace Hyperframework;

class DataLoader {
    public static function load($pathConfigName, $defaultPath, $type) {
        $provider = Config::get(get_called_class() . '\Provider');
        if ($provider !== null) {
            $path = Config::get(
                $pathConfigName, array('default' => $defaultPath)
            );
            return $provider::get($path);
        }
        $path = Config::get($pathConfigName);
        if ($path === null) {
            $path = static::getRootPath($type) .
                $defaultPath . '.' . $type . '.php';
        }
        return require $path;
    }

    private static function getRootPath($type) {
        $result = Config::get(get_called_class() . '\RootPath');
        if ($result === null) {
            return Config::get(
                'Hyperframework\AppRootPath', array('is_nullable' => false)
            ) . $type . DIRECTORY_SEPARATOR;
        }
        return $result;
    }
}
