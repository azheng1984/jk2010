<?php
namespace Hyperframework\Web;

class PathInfo {
    private static $cache;

    public static function get($path) {
        $cache = static::getCache();
        if (isset($cache['paths'][$path]) === false) {
            throw new Exceptions\NotFoundException;
        }
        $info = $cache['paths'][$path];
        $info['namespace'] = static::getNamespace($path);
        return $info;
    }

    public static function reset() {
        static::$cache = null;
    }

    private static function getCache() {
        if (static::$cache === null) {
            static::$cache = \Hyperframework\CacheLoader::load(
                'Hyperframework' . DIRECTORY_SEPARATOR . 'Web'
                    . DIRECTORY_SEPARATOR . 'PathInfo',
                __CLASS__ . '\CachePath'
            );
        }
        return static::$cache;
    }

    private static function getNamespace($path) {
        if (isset(static::$cache['namespace']) === false) {
            return static::$cache['paths'][$path]['namespace'];
        }
        $namespace = static::$cache['namespace'];
        //if (is_array($namespace) === false) {
        if (isset(static::$cache['paths'][$path]['namespace'])) {
            return $namespace. '\\' . static::$cache['paths'][$path]['namespace'];
        }
        return static::$cache['namespace'];
        //}
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
