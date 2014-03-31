<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\CacheLoader;

final class PathInfo {
    private static $cache;

    public static function get($path) {
        $result = null;
        if (Config::get(__CLASS__ . '.cache_enabled') === false) {
            $result = PathInfoBuilder::build($path);
        } else {
            if (self::$cache === null) {
                self::$cache = CacheLoader::load(
                    'path_info.php', __CLASS__ . '.cache_path'
                );
            }
            if (isset(self::$cache[$path]) === false) {
                throw new NotFoundException;
            }
            $result = self::$cache[$path];
        }
        $result['namespace'] = \Hyperframework\APPLICATION_NAMESPACE
            . '\\App\\' . $result['namespace'];
        return $result;
    }

    public static function reset() {
        self::$cache = null;
    }
}
