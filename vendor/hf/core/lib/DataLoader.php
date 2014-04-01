<?php
namespace Hyperframework;
// $cachePath = CacheLoader::getPath();
// if (file_exists($cachePath)) {
//     $cache = require $cachePath;
// }
//
// ... $cache ...
//
// $cache = CacheLoader::load();
//
// $cache = CacheLoader::load('x.php', null,  true);
// if ($cache === null) {
//    ...
// }
class DataLoader {
    final public static function load(
        $defaultPath, $pathConfigName = null, $shouldCheckFileExists = false
    ) {
        $path = self::getPath($defaultPath, $pathConfigName);
        if ($shouldCheckFileExists && file_exists($path) === false) {
            return;
        }
        return require $path;
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
