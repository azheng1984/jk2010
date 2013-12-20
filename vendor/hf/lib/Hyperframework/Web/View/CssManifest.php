<?php
namespace Hyperframework\Web\View;

class CssManifest {
    public static function getUrls($path, $vendor = null) {
        $path = DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR
            . 'css' . DIRECTORY_SEPARATOR . $path;
        if (Config::get(__CLASS__ . '\CacheEnabled')) {
            return require static::getCacheFullPath($path, $vendor);
        }
        $manifest = require static::getManifestFullPath($path, $vendor);
        //fetch build url may be scan dir like class loader or asset proxy
    }

    private static function getFullPath($path, $vendor) {
        $suffix = DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR
            . 'css' . DIRECTORY_SEPARATOR . $path . '.manifest.php';
        if ($vendor === null) {
            return Config::get('Hyperframework\AppPath') . $suffix;
        }
        return Config::get('Hyperframework\AppPath') . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR . $vendor
            . DIRECTORY_SEPARATOR . $suffix;
    }

    private static function getCacheFullPath($path, $vendor) {
        $prefix = Config::get('Hyperframework\AppPath') . DIRECTORY_SEPARATOR
            . 'data' . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'manifest'
            . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
        if ($vendor !== null) {
            $prefix .= 'vendor' . DIRECTORY_SEPARATOR . $vendor
                . DIRECTORY_SEPARATOR;
        }
        return $prefix . $path . '.cache.php';
    }
}
