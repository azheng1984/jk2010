<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\CacheLoader;

final class PathInfo {
    private static $cache;
    private static $builder;
    private static $builderConfig;

    public static function get($path) {
        $result = self::build($path);
        if ($result === null) {
            throw new NotFoundException;
        }
        if ($path[0] !== '#') {
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
        $isCacheEnabled = Config::get(
            'hyperframework.web.path_info.cache_enabled'
        );
        if ($isCacheEnabled === false) {
            $name = null;
            $segments = explode('/', $path);
            array_shift($segments);
            $amount = count($segments);
            $index = 0;
            foreach ($segments as $segment) {
                ++$index;
                $words = explode('_', $segment);
                foreach ($words as $word) {
                    $name .= ucfirst($word);
                }
                if ($index < $amount) {
                    $name .= '\\';
                }
            }
            if (strncmp($path, '#', 1) !== 0) {
                $name = 'App\\' . $name;
            }
            if (self::$builder === null) {
                self::$builderConfig = ConfigLoader::load(
                    'path_info_builder.php',
                    'hyperframework.web.path_info.builder_config_name',
                    true
                );
                if (isset(self::$builderConfig['class'])) {
                    self::$builder = 'Hyperframework\Web\PathInfoBuilder';
                }
            }
            return self::$builder::build($name, self::$builderConfig);
        }
        if (self::$cache === null) {
            self::$cache = CacheLoader::load(
                'path_info.php', 'hyperframework.web.path_info.cache_path'
            );
        }
        if (isset(self::$cache[$path])) {
            return self::$cache[$path];
        }
    }
}
