<?php
namespace Hyperframework\Web\View;

use \Hyperframework\Config;

class AssetUrl {
    public static function get($path, $options = null) {
        $isRelative = isset($options['is_relative']) ?
            $options['is_relative'] : false; 
        $result = $path;
        if ($isRelative === false) {
            $defaultRootPath = isset($options['default_root_path']) ?
                $options['default_root_path'] : false;
            $result = static::appendRoot($path, $defaultRootPath);
        }
        if (Config::get(__CLASS__ . '\CacheVersionEnabled') === false) {
            return static::addCacheVersion($path);
        }
        return $result;
    }

    private static function appendRoot($path, $defaultRootPath) {
        if (substr($path, 0, 1) !== '/' && substr($path, 0, 7) !== 'http://') {
            $rootPath = $defaultRootPath === null ? Config::get(
                __CLASS__ . '\RootPath', array('default' => '/asset')
            ) : $defaultRootPath;
            return $rootPath . '/' . $path;
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
