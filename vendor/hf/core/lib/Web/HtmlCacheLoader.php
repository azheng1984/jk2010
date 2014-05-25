<?php
namespace Hyperframework\Web;

class HtmlCacheLoader {
    private static $basePath;

    public static function load($path) {
        require self::getBasePath() . $path;
    }

    private static function getBasePath() {
        if (self::$basePath !== null) {
            return self::$basePath;
        }
        if (Config::get('hyperframework.web.enable_html_cache') === false) {
            $basePath = Config::get(
                'hyperframework.web.html_cache.dynamic_file_base_path'
            );
            if ($basePath === null) {
                $basePath = Hyperframework\APPLICATION_ROOT_PATH
                    . DIRECTORY_SEPARATOR . 'include'
                    . DIRECTORY_SEPARATOR . 'html_cache' . DIRECTORY_SEPARATOR;
            }
            require $basePath . $path;
            return;
        }
        $basePath = Config::get('hyperframework.web.html_cache.base_path');
        if ($basePath === null) {
            $basePath = Hyperframework\APPLICATION_ROOT_PATH
                . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'cache'
                . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR;
        }
    }
}
