<?php
namespace Hyperframework\Web;

class ApplicationInfo {
    private static $cacheProvider;
    private static $cache;

    public static function initialize($cacheProvider = null) {
        static::$cacheProvider = $cacheProvider;
        static::$cache = null;
    }

    public static function getPathInfo($path = null) {
        if ($path === null) {
            $path = static::getPath();
        }
        $cache = static::getCache();
        if (isset($cache['paths'][$path]) === false) {
            throw new NotFoundException('Path \'' . $path . '\' not found');
        }
        $info = $cache['paths'][$path];
        $info['namespace'] = static::getNamespace($path);
        return $info;
    }

    public static function isPathExists($path) {
        if ($path === null) {
            $path = static::getPath();
        }
        $cache = static::getCache();
        return isset($cache['paths'][$path]);
    }

    private static function getPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $path = $segments[0];
    }

    private static function getCache($path) {
        if (static::$cache === null) {
            static::initializeCache();
        }
        return static::$cache;
    }

    private static function initializeCache() {
        if (static::$cacheProvider === null) {
            static::$cache = require CACHE_PATH . 'application.cache.php';
            return;
        }
        if (is_string(static::$cacheProvider)) {
            static::$cache = require static::$cacheProvider;
            return;
        }
        $provider = new static::$cacheProvider[0];
        static::$cache = $provider->{static::$cacheProvider[1]}();
    }

    private static function getNamespace($path) {
        if (isset(static::$cache['namespace']) === false) {
            return '\\';
        }
        $namespace = static::$cache['namespace'];
        if (is_array($namespace) === false) {
            return '\\' . $namespace. '\\';
        }
        if (isset($namespace['folder_mapping']) === false) {
            throw new \Exception('Application cache format is incorrect');
        }
        $root = isset($namespace['root']) ? $namespace['root'] : null;
        if ($path === '/') {
            return $root === null ? '\\' : '\\' . $root. '\\';
        }
        $root = $root === null ? '' : '\\' . $root;
        return $root . str_replace('/', '\\', $path) . '\\';
    }
}
