<?php
namespace Hyperframework;

class PhpDataFileLoader {
    final public static function load(
        $defaultPath, $pathConfigName = null, $shouldCheckFileExists = false
    ) {
        $path = self::getPath($defaultPath, $pathConfigName);
        if ($shouldCheckFileExists && file_exists($path) === false) {
            return;
        }
        return require $path;
    }

    protected static function getDefaultRootPathSuffix() {
        return DIRECTORY_SEPARATOR . 'data';
    }

    private static function getPath($defaultPath, $pathConfigName = null) {
        $path = null;
        if ($pathConfigName !== null) {
            $path = Config::get($pathConfigName);
        }
        if ($path === null) {
            $path = $defaultPath;
        }
        if (PathTypeRecognizer::isFull($path)) {
            return $path;
        }
        return APPLICATION_PATH . getDefaultRootPathSuffix()
            . DIRECTORY_SEPARATOR . $path;
    }
}
