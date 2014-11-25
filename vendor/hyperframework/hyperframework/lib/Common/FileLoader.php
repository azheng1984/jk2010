<?php
namespace Hyperframework\Common;

use Exception;

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
            return false;
        }
        if (FullPathRecognizer::isFull($path) === false) {
            $path = static::getDefaultBasePath() . DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }

    protected static function getDefaultBasePath() {
        $appRootPath = Config::get('hyperframework.app_root_path');
        if ($appRootPath == '' && (string)$appRootPath === '') {
            throw new Exception;
        }
        return $appRootPath;
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
