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
        return $result;
    }

    public static function reset() {
        self::$cache = null;
    }

    private static function build($path) {
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
        $namespace = null;
        $segments = explode('/', $path);
        array_shift($segments);
        $amount = count($segments);
        $index = 0;
        foreach ($segments as $segment) {
            ++$index;
            $words = explode('_', $segment);
            foreach ($words as $word) {
                $namespace .= ucfirst($word);
            }
            if ($index < $amount) {
                $namespace .= '\\';
            }
        }
        if (strncmp($path, '#', 1) !== 0) {
            $namespace = 'App\\' . $namespace;
        } else {
            $namespace =substr($namespace, 1);
        }
        $config = ConfigLoader::load(
            'path_info_builder.php',
            'hyperframework.path_info.builder_config_path'
        );
        $builder = __NAMESPACE__ . '\PathInfoBuilder';
        $options = null;
        if ($config !== null) {
            if (isset($config['class'])) {
                $builder = $config['class'];
                unset($config['class']);
            }
            if (isset($config['options'])) {
                $options = $config['options'];
            }
        }
        return $builder::build(
            \Hyperframework\APPLICATION_ROOT_PATH . DIRECTORY_SEPARATOR
                . 'lib' . DIRECTORY_SEPARATOR . $namespace,
            \Hyperframework\APPLICATION_ROOT_NAMESPACE . '\\' . $namespace,
            $options
        );
    }
}
