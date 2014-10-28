<?php
namespace Hyperframework;

class FileLoader {
    final public static function loadPhp(
        $defaultPath, $pathConfigName = null, $shouldCheckFileExists = false
    ) {
        return self::load(
            $defaultPath, $pathConfigName, $shouldCheckFileExists, true
        );
    }

    final public static function loadData(
        $defaultPath, $pathConfigName = null, $shouldCheckFileExists = false
    ) {
        return self::load(
            $defaultPath, $pathConfigName, $shouldCheckFileExists, false
        );
    }

    final public static function getPath($defaultPath, $pathConfigName = null) {
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
        if ($path === null) {
            return;
        }
        if (FullPathRecognizer::isFull($path) === false) {
            $path = static::getDefaultBasePath() . DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }

    protected static function getDefaultBasePath() {
        return APP_ROOT_PATH;
    }

    final private static function load(
        $defaultPath, $pathConfigName, $shouldCheckFileExists, $isPhp
    ) {
        $path = self::getPath($defaultPath);
        if ($path === null) {
            return;
        }
        if ($shouldCheckFileExists && file_exists($path) === false) {
            return;
        }
        if ($isPhp) {
            return require $path;
        }
        return file_get_contents($path);
    }
}
