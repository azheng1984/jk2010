<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\CacheLoader;

final class PathInfo {
    private static $cache;

    public static function get($relativeUrl) {
        $result = self::build($relativeUrl);
        if ($result === null) {
            throw new NotFoundException;
        }
        if (strncmp($relativeUrl, '#', 1) !== 0) {
            $result['namespace'] =  '\App\\' . $result['namespace'];
        }
        $result['namespace'] = \Hyperframework\APPLICATION_NAMESPACE
            . $result['namespace'];
        return $result;
    }

    public static function reset() {
        self::$cache = null;
    }

    private static function build($relativeUrl) {
        if (Config::get(__CLASS__ . '.cache_enabled') === false) {
            $segments = explode('/', $relativeUrl);
            foreach ($segments as $segment) {
                $words = explode('_', $segment);
                foreach $words;
            }
            if (strncmp($path, '#', 1) !== 0) {
                $basePath = '\App\\' . $basePath;
            }
            $basePath = \Hyperframework\APPLICATION_NAMESPACE
                . $basePath;
            return PathInfoBuilder::build($basePath);
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
