<?php
namespace Hyperframework\Web;

class PathInfo {
    private static $cache;

    public static function get($path = null) {
        if ($path === null) {
            $path = static::getPath();
        }
        $cache = static::getCache();
        if (isset($cache['paths'][$path]) === false) {
            throw new Exceptions\NotFoundException;
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
            static::$cache = \Hyperframework\CacheLoader::load(
                __CLASS__ . '\CachePath', 'path_info'
            );
        }
        return static::$cache;
    }

    private static function getNamespace($path) {
        if (isset(static::$cache['namespace']) === false) {
            return '\\';
        }
        $namespace = static::$cache['namespace'];
        if (is_array($namespace) === false) {
            return '\\' . $namespace. '\\';
        }
        //throw ...
//        if (isset($namespace['folder_mapping']) === false) {
//            throw new \Exception('Format of path info cache is not correct');
//        }
//        $root = isset($namespace['root']) ? $namespace['root'] : null;
//        if ($path === '/') {
//            return $root === null ? '\\' : '\\' . $root. '\\';
//        }
//        $root = $root === null ? '' : '\\' . $root;
//        return $root . str_replace('/', '\\', $path) . '\\';
    }
}
