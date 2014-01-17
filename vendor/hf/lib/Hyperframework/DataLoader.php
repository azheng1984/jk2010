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
            $path = $path . $extension;
        }
        return $path;
    }

    protected static function getDefaultRootPath() {
        return Config::get(
            __NAMESPACE__ . '\AppPath',
            array(
                'default' => array('app_const' => 'ROOT_PATH'),
                'is_nullable' => false
            )
        );
    }

    protected static function getFileNameExtension() {}

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
