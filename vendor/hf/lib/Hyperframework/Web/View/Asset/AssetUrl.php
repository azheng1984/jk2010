<?php
namespace Hyperframework\Web\View\Asset;

use \Hyperframework\Config;

class AssetUrl {
    private static $baseUrl;

    public static function get($path, $isRelative = false) {
        if ($isRelative === false) {
           $path = static::appendRoot($result);
        }
        if (Config::get(__CLASS__ . '\CacheVersionEnabled') === false) {
            return static::insertCacheVersion($path, $isRelative);
        }
        return $path;
    }

    public static function setBaseUrl($value) {
        static::$baseUrl = $value;
    }

    protected static function getDefaultRootPath() {
        return '/asset';
    }

    private static function appendRoot($path) {
        if (substr($path, 0, 1) !== '/' && substr($path, 0, 7) !== 'http://') {
            return Config::get(
                get_called_class() . '\RootPath',
                array('default' => static::getDefaultRootPath())
            ) . '/' . $path;
        }
        return $path;
    }

    private static function insertCacheVersion($path, $isRelative) {
        $cacheVersion = static::getCacheVersion($path, $isRelative);
        if ($cacheVersion === null) {
            return $path;
        }
        $name = basename($path);
        $lastDotPosition = strrpos($name, '.');
        if ($lastDotPosition === false) {
            return $path . '-' . $cacheVersion;
        }
        return dirname($path) . '/' . substr($name, 0, $lastDotPosition)
            . '.' . $cacheVersion . substr($name, $lastDotPosition);
    }

    private static function getCacheVersion($path, $isRelative) {
        if ($isRelative === false) {
            return AssetCacheVersion::get($path);
        }
        if (static::$baseUrl === null) {
            throw new \Exception(
                'Base url not set when getting relative path cache version'
            );
        }
        return static::getCacheVersion(static::$baseUrl . '/' . $path);
    }
}
