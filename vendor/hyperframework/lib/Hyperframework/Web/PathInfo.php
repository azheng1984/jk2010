<?php
namespace Hyperframework\Web;

class PathInfo {
    private static $cacheProvider;
    private static $cache;
    private static $mode;

    public static function setCacheProvider($cacheProvider) {
        static::$cacheProvider = $cacheProvider;
        static::$cache = null;
    }

    public static function setMode($mode) {
        static::$mode = $mode;
    }

    public static function get($path = null) {
        if ($path === null) {
            $path = static::getPath();
        }
        $cache = static::getCache();
        if (isset($cache['paths'][$path]) === false) {
            throw new NotFoundException;
        }
        $info = $cache['paths'][$path];
        $info['namespace'] = static::getNamespace($path);
        return $info;
    }

    public static function exists($path) {
        if ($path === null) {
            $path = static::getPath();
        }
        $cache = static::getCache();
        return isset($cache['paths'][$path]);
    }

    private static function getPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        return $segments[0];
    }

    private static function getCache() {
        if (static::$cache === null) {
            static::initializeCache();
        }
        return static::$cache;
    }

    private static function initializeCache() {
        if (static::$cacheProvider === null) {
            static::$cache = require CACHE_PATH . 'path_info.cache.php';
            return;
        }
        if (is_string(static::$cacheProvider)) {
            static::$cache = require static::$cacheProvider;
            return;
        }
        $providerClass = static::$cacheProvider[0];
        $providerClass::{static::$cacheProvider[1]}();
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
            throw new \Exception('Format of path info cache is not correct');
        }
        $root = isset($namespace['root']) ? $namespace['root'] : null;
        if ($path === '/') {
            return $root === null ? '\\' : '\\' . $root. '\\';
        }
        $root = $root === null ? '' : '\\' . $root;
        return $root . str_replace('/', '\\', $path) . '\\';
    }
}
