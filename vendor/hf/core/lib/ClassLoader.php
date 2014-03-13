<?php
namespace Hyperframework;

class ClassLoader {
    private static $cacheEnabled;

    public static function run() {
        spl_autoload_register(array(__CLASS__, 'load'));
        static::$cacheEnabled = Config::get(__NAMESPACE__ . '\CacheEnabled');
        $classLocator = Config::get(__NAMESPACE__ . '\ClassLocator', function() {
            return __DIR__ . DIRECTORY_SEPARATOR . 'ClassLocator.php';
        });
        if (static::$cacheEnabled === false) {
            require ;
        }
    }

    public static function load($name) {
        require static::getPath($name);
    }

    protected static function getConfig() {
        ConfigLoader::get('class_loader', __CLASS__ . '\CachePath');
    }

    private static function getPath($name) {
        if (static::$cacheEnabled) {
            return static::getPathFromCache($name);
        }
        return ClassLocator::getPath($name);
    }

    private static function getPathFromCache($name) {
    }
}
