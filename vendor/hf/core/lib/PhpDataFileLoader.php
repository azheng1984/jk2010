<?php
namespace Hyperframework;

class PhpDataFileLoader {
    final public static function load(
        $defaultPath, $pathConfigName = null, $shouldCheckFileExists = false
    ) {
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
        if (PathTypeRecognizer::isFull($path) === false) {
            $path = static::getDefaultRootPath() . DIRECTORY_SEPARATOR . $path;
        }
        if ($shouldCheckFileExists && file_exists($path) === false) {
            return;
        }
        return require $path;
    }

    protected static function getDefaultRootPath() {
        return APPLICATION_PATH;
    }
}
