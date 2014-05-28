<?php
namespace Hyperframework\Web\Html;

class CacheLoader {
    private static $basePath;

    public static function load($path) {
        require self::getBasePath() . $path;
    }

    public static function getDynamicFileBasePath() {
    }

    public static function getCacheFileBasePath() {
    }

    private static function getBasePath() {
        if (self::$basePath !== null) {
            return self::$basePath;
        }
        if (Config::get('hyperframework.web.enable_html_fragment_cache') === false) {
            $basePath = Config::get(
                'hyperframework.web.html_fragment_cache.dynamic_file_base_path'
            );
            if ($basePath === null) {
                $basePath = Hyperframework\APPLICATION_ROOT_PATH
                    . DIRECTORY_SEPARATOR . 'include'
                    . DIRECTORY_SEPARATOR . 'html_cache' . DIRECTORY_SEPARATOR;
            }
            require $basePath . $path;
            return;
        }
        $basePath = Config::get(
            'hyperframework.web.html_fragment_cache.cache_file_base_path'
        );
        if ($basePath === null) {
            $basePath = Hyperframework\APPLICATION_ROOT_PATH
                . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'cache'
                . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR;
        }
    }
}
