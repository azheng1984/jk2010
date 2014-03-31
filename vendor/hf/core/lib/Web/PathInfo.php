<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\CacheLoader;

final class PathInfo {
    private static $cache;

    public static function get($path) {
        $result = self::build($path);
        if ($result === null) {
            throw new NotFoundException;
        }
        if (strncmp($path, '#', 1) === 0) {
            $result['namespace'] =  '\App\\' . $result['namespace'];
        }
        $result['namespace'] = \Hyperframework\APPLICATION_NAMESPACE
            . $result['namespace'];
        return $result;
    }

    public static function reset() {
        self::$cache = null;
    }

    private static function build($path) {
        if (Config::get(__CLASS__ . '.cache_enabled') === false) {
            return PathInfoBuilder::build($path);
        }
        if (self::$cache === null) {
            self::$cache = CacheLoader::load(
                'path_info.php', __CLASS__ . '.cache_path'
            );
        }
        if (isset(self::$cache[$path])) {
            return self::$cache[$path];
        }
    }
}
