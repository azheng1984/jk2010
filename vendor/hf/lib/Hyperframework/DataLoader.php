<?php
namespace Hyperframework;

abstract class DataLoader {
    public static function load(
        $path, $pathConfigName, $hasEnv = false, $isRelativePath = true
    ) {
        return parent::executeTemplate(
            $path, $pathConfigName, $hasEnv, $isRelativePath
        );
    }

    protected static final function executeTemplate(
        $path, $pathConfigName = null, $isRelativePath = true
    ) {
        //var_dump($path);
        $class = get_called_class();
        $delegate = Config::get($class . '\Delegate');
        if ($delegate !== null) {
            return $delegate::load($path, $pathConfigName, $isRelativePath);
        }
        if ($pathConfigName !== null) {
            $config = Config::get($pathConfigName);
            if ($config === null) {
                if ($isRelativePath) {
        //            var_dump(static::getRootPath($class));
                    $path = static::getRootPath($class)
                        . DIRECTORY_SEPARATOR . $path;
                }
                $path = $path . static::getFileNameExtension();
            } else {
                $path = $config;
            }
        }
        return static::loadByPath($path);
    }

    protected static function getRootPath($class) {
        $result = Config::get($class . '\RootPath');
        if ($result !== null) {
            return $result;
        }
        return static::getDefaultRootPath();
    }

    protected static function getFileNameExtension() {
        return '.php';
    }

    protected static function getDefaultRootPath() {
        return static::getAppPath() . DIRECTORY_SEPARATOR
            . static::getDefaultRootPathSuffix();
    }

    protected static function getDefaultRootPathSuffix() {
        return 'data';
    }

    protected static function getAppPath() {
        return \Yxj\ROOT_PATH;
        return Config::get(
            __NAMESPACE__ . '\AppPath',
            array(
                'default' => array('app_const' => 'ROOT_PATH'),
                'is_nullable' => false
            )
        );
    }

    protected static function loadByPath($fullPath) {
        return require $fullPath;
    }
}
