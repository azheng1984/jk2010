<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\CacheLoader;

final class PathInfo {
    private static $cache;
    private static $builder;
    private static $builderConfig;

    public static function get($urlPath) {
        $result = self::build($urlPath);
        if ($result === null) {
            throw new NotFoundException;
        }
        if ($urlPath[0] !== '#') {
            $result['namespace'] =  '\App\\' . $result['namespace'];
        }
        $result['namespace'] = \Hyperframework\APPLICATION_NAMESPACE
            . $result['namespace'];
        return $result;
    }

    public static function reset() {
        self::$cache = null;
    }

    private static function buildByUrlPath($urlPath) {
        $isCacheEnabled = Config::get(
            'hyperframework.web.path_info.enable_cache'
        );
        if ($isCacheEnabled) {
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
        if (self::$builder === null) {
            self::$builderConfig = ConfigLoader::load(
                'path_info_cache_builder.php',
                'hyperframework.web.path_info.builder_config_name',
                true
            );
            if (isset(self::$builderConfig['class'])) {
                self::$builder = self::$builderConfig['class'];
            } else {
                self::$builder = 'Hyperframework\Web\PathInfoCacheBuilder';
            }
        }
        return self::$builder::build(
            \Hyperframework\APPLICATION_PATH . DIRECTORY_SEPARATOR
                . 'lib' . DIRECTORY_SEPARATOR . $namespace,
            \Hyperframework\APPLICATION_NAMESPACE . '\\' . $namespace,
            self::$builderConfig,
        );
    }

    public static function buildByFileSystemPath($fileSystemPath) {
    }
}
