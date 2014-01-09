<?php
namespace Hyperframework;

abstract class DataLoader {
    public static function load(
        $path, $pathConfigName = null, $isRelativePath = true
    ) {
        $class = get_called_class();
        $delegate = Config::get($class . '\Delegate');
        if ($delegate !== null) {
            if ($pathConfigName !== null) {
                $config = Config::get(
                    $pathConfigName, array('default' => $path)
                );
                if ($config !== $path) {
                    $isRelativePath = false;
                }
                $path = $config;
            }
            return $delegate::get($path, $isRelativePath);
        }
        $path = Config::get(
            $pathConfigName, array('default' => $path)
        );
        if ($pathConfigName !== null) {
            $config = Config::get($pathConfigName);
            if ($config === null) {
                if ($isRelativePath) {
                    $path = static::getRootPath() . DIRECTORY_SEPARATOR . $path;
                }
                $path = $path . static::getDefaultFileNameExtension();
            } else {
                $path = $config;
            }
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
            . static::getDefaultInAppRootPath();
    }

    protected static function getDefaultInAppRootPath() {
        return 'data';
    }

    protected static function loadContent($path) {
        return require $path;
    }
}
