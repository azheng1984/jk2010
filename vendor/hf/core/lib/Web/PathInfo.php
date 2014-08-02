<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\CacheFileLoader;
use Hyperframework\ConfigFileLoader;

final class PathInfo {
    public static function get($path, $type = 'App') {
        $result = null;
        if (Config::get('hyperframework.path_info.enable_cache') !== false) {
            $cacheFolder = Config::get('hyperframework.path_info.cache_folder');
            if ($cacheFolder === null) {
                $cacheFolder = 'path_info';
            }
            if (self::$cache === null) {
                self::$cache = CacheFileLoader::loadPhp(
                    $cacheFolder . DIRECTORY_SEPARATOR . $type . '.php'
                );
            }
            if (isset(self::$cache[$path])) {
                $result = self::$cache[$path];
            }
        } else {
            $result = self::build($path, $type);
        }
        if ($result === null) {
            throw new NotFoundException;
        }
        return $result;
    }

    private static function build($path, $type) {
        $builderConfig = ConfigFileLoader::loadPhp(
            'path_info_builder.php',
            'hyperframework.path_info.builder_config_path',
            true
        );
        $builder = __NAMESPACE__ . '\PathInfoBuilder';
        $options = null;
        if ($config !== null) {
            if (isset($config['class'])) {
                $builder = $config['class'];
            }
            if (isset($config['options'])) {
                $options = $config['options'];
            }
        }
        return $builder::build($path, $type, $options);
    }
}
