<?php
namespace Hyperframework;

class FileLoader {
    final public static function loadPhp($path, $pathConfigName = null) {
        return self::load($path, $pathConfigName, true);
    }

    final public static function loadData($path, $pathConfigName = null) {
        return self::load($path, $pathConfigName, false);
    }

    final public static function getFullPath($path, $pathConfigName = null) {
        if ($pathConfigName !== null) {
            $tmp = Config::get($pathConfigName);
            if ($tmp !== null) {
                $path = $tmp;
            }
        }
        if ($path == '' && (string)$path === '') {
            return;
        }
        if (FullPathRecognizer::isFull($path) === false) {
            $path = static::getDefaultBasePath() . DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }

    private static function hasFile($path, $pathConfigName = null) {
        $path = self::getFullPath($path, $pathConfigName);
        if ($path === null) {
            return false;
        }
        return file_exists($path);
    }

    protected static function getDefaultBasePath() {
        return APP_ROOT_PATH;
    }

    private static function load($path, $pathConfigName, $isPhp) {
        $path = self::getFullPath($path, $pathConfigName);
        if ($path === null) {
            throw new Exception;
        }
        if ($isPhp) {
            return include $path;
        }
        return file_get_contents($path);
    }
}
