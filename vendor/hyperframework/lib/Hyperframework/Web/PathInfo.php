<?php
namespace Hyperframework\Web;
use Hyperframework\Config;

class PathInfo {
    private static $cache;

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

    public static function reset() {
        static::$cache = null;
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
        $cacheProvider = Config::get('Hyperframework\AppCacheProvider');
        if ($cacheProvider === null) {
            static::$cache = require Config::get(
                __CLASS__ . '\CachePath',
                constant(Hyperframwork\APP_NAMESPACE . 'CACHE_PATH')
                    . 'path_info.cache.php'
            )
            return;
        }
        static::$cache = $cacheProvider::get(
            Config::get(__CLASS__ . '\CachePath', 'path_info')
        );
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
