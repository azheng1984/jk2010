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
        $isCacheEnabled = Config::get(
            'hyperframework.web.path_info.cache_enabled'
        );
        if ($isCacheEnabled === false) {
            $path = null;
            $segments = explode('/', $relativeUrl);
            array_shift($segments);
            $amount = count($segments);
            $index = 0;
            foreach ($segments as $segment) {
                ++$index;
                $words = explode('_', $segment);
                foreach ($words as $word) {
                    $path .= ucfirst($word);
                }
                if ($index < $amount) {
                    $path .= '\\';
                }
            }
            if (strncmp($path, '#', 1) !== 0) {
                $path = 'App\\' . $path;
            }
            $builder = ConfigLoader::load(
                'path_info_builder.php',
                'hyperframework.web.path_info.builder_config_name',
                true
            );
            if ($builder === null) {
                $builder = 'Hyperframework\Web\PathInfoBuilder';
            }
            return $builder::build(
                \Hyperframework\APPLICATION_NAMESPACE . '\\' . $path
            );
        }
        if (self::$cache === null) {
            self::$cache = CacheLoader::load(
                'path_info.php', 'hyperframework.web.path_info.cache_path'
            );
        }
        if (isset(self::$cache[$relativeUrl])) {
            return self::$cache[$relativeUrl];
        }
    }
}
