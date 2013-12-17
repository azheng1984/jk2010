<?php
namespace Hyperframework\Web\View\Asset;

use \Hyperframework\Config;

class AssetUrl {
    public static function get($path, $isRelative = false) {
        $result = $path;
        if ($isRelative === false) {
           $result = static::appendRoot($path);
        }
        if (Config::get(__CLASS__ . '\CacheVersionEnabled') === false) {
            return static::addCacheVersion($path);
        }
        return $result;
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

    private static function addCacheVersion($path) {
        $cacheVersion = AssetCacheVersion::get($path);
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
}
