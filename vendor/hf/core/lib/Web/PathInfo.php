<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\CacheLoader;

final class PathInfo {
    private static $cache;

    public static function get($urlPath) {
        $result = self::build($urlPath);
        if ($result === null) {
            throw new NotFoundException;
        }
        return $result;
    }

    public static function reset() {
        self::$cache = null;
    }

    private static function build($urlPath) {
        $isCacheEnabled = Config::get(
            'hyperframework.web.path_info.enable_cache'
        );
        if ($isCacheEnabled !== false) {
            if (self::$cache === null) {
                self::$cache = CacheLoader::load(
                    'path_info.php', 'hyperframework.web.path_info.cache_path'
                );
            }
            if (isset(self::$cache[$path])) {
                return self::$cache[$path];
            }
            return;
        }
        $path = null;
        $segments = explode('/', $path);
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
        } else {
            $path =substr($path, 1);
        }
        $config = ConfigLoader::load(
            'path_info_builder.php',
            'hyperframework.path_info_builder.config_path'
        );
        $builder = Config::get('hyperframework.web.path_info.builder');
        if ($builder === null) {
            $builder = __NAMESPACE__ . '\PathInfoBuilder';
        }
        return $builder::build(
            \Hyperframework\APPLICATION_PATH . DIRECTORY_SEPARATOR
                . 'lib' . DIRECTORY_SEPARATOR . $namespace,
            \Hyperframework\APPLICATION_NAMESPACE . '\\' . $namespace,
        );
    }
}
