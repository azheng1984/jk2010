<?php
namespace Hyperframework;
// $cachePath = CacheLoader::getPath();
// if (file_exists($cachePath)) {
//     $cache = require $cachePath;
// }
class DataLoader {
    final public static function load($defaultPath, $pathConfigName = null, $shouldCheckFileExists = false) {
        return require self::getPath($defaultPath, $pathConfigName);
    }

    final public static function getPath($defaultPath, $pathConfigName = null) {
        $path = null;
        if ($pathConfigName !== null) {
            $path = Config::get($pathConfigName);
        }
        if ($path === null) {
            $path = $defaultPath;
        }
        return self::getFullPath($path);
    }

    protected static function getDefaultRootPathSuffix() {
        return DIRECTORY_SEPARATOR . 'data';
    }

    private static function getFullPath($path) {
        if (PathTypeRecognizer::isFull($path)) {
            return $path;
        }
        return APPLICATION_PATH . getDefaultRootPathSuffix()
            . DIRECTORY_SEPARATOR . $path;
    }
}
