<?php
namespace Hyperframework;

abstract class DataLoader {
    public static function load(
        $path, $pathConfigName = null, $isRelativePath = true
    ) {
        $class = get_called_class();
        $provider = Config::get($class . '\Provider');
        if ($provider !== null) {
            if ($pathConfigName !== null) {
                $path = Config::get(
                    $pathConfigName, array('default' => $path)
                );
            }
            return $provider::get($path, $isRelative);
        }
        $path = Config::get(
            $pathConfigName, array('default' => $path)
        );
        if ($pathConfigName !== null) {
            $path = Config::get($pathConfigName);
        }
        if ($path === null) {
            $defaultPath = static::getRootPath($class) . DIRECTORY_SEPARATOR
                . $path . static::getDefaultFileNameExtension();
        }
        static::loadContent($path);
    }

    protected static function getAppPath() {
        return Config::get(
            __NAMESPACE__ . '\AppPath',
            array(
                'default' => array('app_const' => 'ROOT_PATH'),
                'is_nullable' => false
            )
        );
        Config::set('cnf_name', ROOT_PATH . '/list/config/path');
    }

    protected static function getFullPath() {
    }

    protected static function getRootPath($class) {
        $result = Config::get($class . '\RootPath');
        if ($result !== null) {
            return $result;
        }
        return static::getDefaultRootPath();
    }

    protected static function getDefaultFileNameExtension() {
        return '.php';
    }

    protected static function getDefaultRootPath() {
        $appPath = static::getAppPath();
        if ($appPath === null) {
            throw new Exception;
        }
        return $appPath . DIRECTORY_SEPARATOR
            . static::getDefaultRootPathByApp();
    }

    protected static function getDefaultRootPathByApp() {
        return 'data';
    }

    protected static function loadContent($path) {
        return require $path;
    }
}
