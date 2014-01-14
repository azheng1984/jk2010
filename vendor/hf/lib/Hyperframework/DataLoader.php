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
        if ($pathConfigName !== null) {
            $configPath = Config::get($pathConfigName);
            if ($configPath === null) {
                if ($isRelativePath) {
                    $path = static::getRootPath($class)
                        . DIRECTORY_SEPARATOR . $path;
                }
                $path = $path . static::getFileNameExtension();
            } else {
                $path = $configPath;
            }
        }
        return static::loadByPath($path);
    }

    protected static function getFileNameExtension() {
        return '.php';
    }

    protected static function getDefaultRootPath() {
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

    private static function getRootPath($class) {
        $result = Config::get($class . '\RootPath');
        if ($result !== null) {
            return $result;
        }
        return static::getDefaultRootPath();
    }
}
