<?php
namespace Hyperframework;

class DataLoader {
    final public static function load($defaultPath, $pathConfigName = null) {
        $path = null;
        if ($pathConfigName !== null) {
            $path = Config::get($pathConfigName);
        }
        if ($path === null) {
            $path = $defaultPath;
        }
        return require self::getFullPath($path);
    }

    protected static function getDefaultRootPathSuffix() {
        return DIRECTORY_SEPARATOR . 'data';
    }

    private static function getFullPath($path) {
        if (PathTypeRecognizer::isFull($path)) {
            return $path;
        }
        return Config::getApplicationPath() . getDefaultRootPathSuffix()
            . DIRECTORY_SEPARATOR . $path;
    }
}
