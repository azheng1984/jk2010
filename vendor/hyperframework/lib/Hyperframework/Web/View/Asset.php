<?php
namespace Hyperframework\Web\View;
use Hyperframework\Config;

class Asset {
    private static $cache;
    private static $config;

    private static function getCache() {
        if (self::$cache === null) {
            static::$cache = \Hyperframework\CacheLoader::load(
                __CLASS__ . '\CachePath', 'asset'
            );
        }
        return self::$cache;
    }

    private static function getConfig() {
        if (self::$config === null) {
            static::$config = \Hyperframework\ConfigLoader::load(
                __CLASS__ . '\ConfigPath', 'asset'
            );
        }
        return self::$config;
    }

//    private static function getPath($path) {
//        $cache = self::getCache();
//        if (isset($cache[$path])) {
//            return $cache[$path];
//        }
//        $url = Asset::getUrl('/js/common', 'js');
//        $url = Asset::getUrl('/js/app', 'js');
//    }

    public static function getUrl($path, $extension, $options = null) {
        $extension = '.' . $extension;
        $config = self::getConfig();
        if (isset($config['path'])) {
        }
        $cache = self::getCache();
        if (Config::get(__CLASS__ . '\EnablePrecompilation' === false)) {
            return $path . $extension;
        }
        return $path . $cache[$path] . $extension;
    }
}
