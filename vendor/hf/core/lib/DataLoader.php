<?php
namespace Hyperframework;

class DataLoader {
    public static function load($defaultPath, $pathConfigName = null) {
        $delegate = Config::get(get_called_class() . '\Delegate');
        if ($delegate !== null) {
            return $delegate::load($defaultPath, $pathConfigName);
        }
        return static::load(
            static::getFullPath($defaultPath, $pathConfigName)
        );
    }

    protected static function getFullPath($defaultPath, $pathConfigName) {
        $result = null;
        if ($pathConfigName !== null) {
            $result = Config::get($pathConfigName);
        }
        if ($result !== null) {
            return $result;
        }
        return static::getDefaultRootPath() . DIRECTORY_SEPARATOR
            . $defaultPath . static::getDefaultFileNameExtension();
    }

    protected static function getDefaultFileNameExtension() {
        return '.php';
    }

    protected static function getDefaultRootPath() {
        return Config::getApplicationPath() . DIRECTORY_SEPARATOR . 'data';
    }

    protected static function load($path) {
        return require $path;
    }
}
