<?php
namespace Hyperframework;

class PhpDataFileLoader {
    final public static function load(
        $defaultPath, $pathConfigName = null, $shouldCheckFileExists = false
    ) {
        $path = self::getPath($defaultPath, $pathConfigName);
        if ($path === null) {
            return;
        }
        if ($shouldCheckFileExists && file_exists($path) === false) {
            return;
        }
        return require $path;
    }

    protected static function getDefaultRootPathSuffix() {
        return APPLICATION_PATH;
    }

    private static function getPath($defaultPath, $pathConfigName = null) {
        $path = null;
        if ($pathConfigName !== null) {
            $path = Config::get($pathConfigName);
        }
        if ($path === false) {
            return;
        }
        if ($path === null) {
            $path = $defaultPath;
        }
        if (PathTypeRecognizer::isFull($path)) {
            return $path;
        }
        return static::getDefaultRootPathSuffix() . DIRECTORY_SEPARATOR . $path;
    }
}
