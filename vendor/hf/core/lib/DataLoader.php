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
        if (self::isRelativePath($path) === false) {
            return $path;
        }
        return Config::getApplicationPath() . getDefaultRootPathSuffix()
            . DIRECTORY_SEPARATOR . $path;
    }

    private static function isRelativePath($path) {
        if (DIRECTORY_SEPARATOR === '/') {
            return strncmp($path, '/', 1) !== 0;
        }
        return substr($path, 1, 1) !== ':' && strncmp($path, '\\', 1) !== 0;
    }
}
