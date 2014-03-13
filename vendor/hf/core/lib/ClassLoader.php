<?php
namespace Hyperframework;

class ClassLoader {
    private static $isCacheEnabled;
    private static $config;
    private static $cache;

    public static function run() {
        spl_autoload_register(array(__CLASS__, 'load'));
        self::$isCacheEnabled = Config::get(__NAMESPACE__ . '\CacheEnabled');
   }

    public static function load($name) {
        require static::getPath($name);
    }

    private static function getPath($name) {
        if (static::$isCacheEnabled) {
            return static::getPathByCache($name);
        }
        return static::getPathByConfig($name);
    }

    private static function getPathByCache($name) {
    }

    private static function getPathByConfig($name) {
    }

//    protected static function loadClassLocator() {
//        require __DIR__ . DIRECTORY_SEPARATOR . 'ClassLocator.php';
//        return __NAMESPACE__ . '\ClassLocator';
//    }

    protected static function loadConfig() {
        return ConfigLoader::get('class_loader', __CLASS__ . '\ConfigPath');
    }

    protected static function loadCache() {
        return CacheLoader::get('class_loader', __CLASS__ . '\CachePath');
    }
}
