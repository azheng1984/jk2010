<?php
namespace Hyperframework\Web\View;

use \Hyperframework\Config;

class AssetUrl {
    private static $baseUrl;

    public static function get($path, $isRelative = false) {
        if ($isRelative === false) {
           $path = static::appendRoot($path);
        }
        if (Config::get('Hyperframework\Web\CacheVersionEnabled') !== false) {
            return static::addCacheVersion($path, $isRelative);
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

    private static function addCacheVersion($path, $isRelative) {
        $cacheVersion = static::getCacheVersion($path, $isRelative);
        if ($cacheVersion === null) {
            return $path;
        }
        $fileName = basename($path);
        $lastDotPosition = strrpos($fileName, '.');
        if ($lastDotPosition === false) {
            return $path . '-' . $cacheVersion;
        }
        return dirname($path) . '/' . substr($fileName, 0, $lastDotPosition)
            . '.' . $cacheVersion . substr($fileName, $lastDotPosition);
    }

    private static function getCacheVersion($path, $isRelative) {
        if ($isRelative === false) {
            return AssetCacheVersion::get($path);
        }
        if (static::$baseUrl === null) {
            throw new \Exception(
                'Base url not set when getting cache version of relative path'
            );
        }
        return static::getCacheVersion(static::$baseUrl . '/' . $path);
    }
}
