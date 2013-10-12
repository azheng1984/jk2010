<?php
namespace Hyperframework\Web\View;

class Asset {
    private static $cache;

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

    private static function getVersion($path) {
        $cache = self::getCache();
        if (isset($cache[$path])) {
            return $cache[$path];
        }
    }

    public static function getUrl($path) {
        //根据 include path 
    }
}
