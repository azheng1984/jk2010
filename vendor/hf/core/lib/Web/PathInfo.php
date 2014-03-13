<?php
namespace Hyperframework\Web;

class PathInfo {
    private static $cache;

    public static function get($path) {
        if (self::$cache === null) {
            self::$cache = static::loadCache();
        }
        $cache = self::$cache;
        if (isset($cache['paths'][$path]) === false) {
            throw new Exceptions\NotFoundException;
        }
        $result = $cache['paths'][$path];
        $result['namespace'] = static::getNamespace($path, $cache);
        return $result;
    }

    public static function reset() {
        static::$cache = null;
    }

    protected static function loadCache() {
        return \Hyperframework\CacheLoader::load(
            'path_info', __CLASS__ . '\CachePath'
        );
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
