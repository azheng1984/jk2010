<?php
namespace Hyperframework;

class DataLoader {
    public static function load(
        $type, $pathConfigName, $defaultPath, $defaultRootPath
    ) {
        $provider = Config::get(get_called_class() . '\Provider');
        if ($provider !== null) {
            $path = Config::get(
                $pathConfigName, array('default' => $defaultPath)
            );
            return $provider::get($path);
        }
        $path = Config::get($pathConfigName);
        if ($path === null) {
            $path = static::getRootPath($defaultRootPath) .
                DIRECTORY_SEPARATOR . $defaultPath . '.' . $type . '.php';
        }
        return require $path;
    }

    private static function getRootPath($defaultRootPath) {
        $result = Config::get(get_called_class() . '\RootPath');
        if ($result === null) {
            return Config::get(
                __NAMESPACE__ . '\AppPath', array('is_nullable' => false)
            ) . DIRECTORY_SEPARATOR . $defaultRootPath;
        }
        return $result;
    }
}
