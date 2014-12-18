<?php
namespace Hyperframework\Common;

use Exception;

class FileLoader {
    private static $defaultRootPath;

    public static function loadPhp($path, $pathConfigName = null) {
        return self::load($path, $pathConfigName, true);
    }

    public static function loadData($path, $pathConfigName = null) {
        return self::load($path, $pathConfigName, false);
    }

    public static function getFullPath($path, $pathConfigName = null) {
        if ($pathConfigName !== null) {
            $tmp = Config::getString($pathConfigName);
            if ($tmp !== null) {
                $path = $tmp;
            }
        }
        $path = (string)$path;
        if ($path === '') {
            return false;
        }
        if (FullPathRecognizer::isFull($path) === false) {
            $path = static::getDefaultRootPath() . DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }

    public static function getDefaultRootPath() {
        if (self::$defaultRootPath === null) {
            self::$defaultRootPath = Config::getString(
                'hyperframework.app_root_path', ''
            );
            if (FullPathRecognizer::isFull(self::$defaultRootPath) === false) {
                throw new Exception(
                    "Config 'hyperframework.app_root_path'"
                        . " is not a correct full path."
                );
            }
        }
        return self::$defaultRootPath;
    }

    private static function load($path, $pathConfigName, $isPhp) {
        $path = self::getFullPath($path, $pathConfigName);
        if ($path === false) {
            throw new Exception;
        }
        if ($isPhp) {
            return include $path;
        }
        return file_get_contents($path);
    }
}
