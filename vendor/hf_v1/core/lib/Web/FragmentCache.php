<?php
namespace Hyperframework\Web;

//todo fragment cache builder
class FragmentCache {
    private static $basePath;

    public static function load($path) {
        require self::getBasePath() . $path;
    }

    public static function getSourceFileBasePath() {
        $result = Config::get(
            'hyperframework.fragment_cache.source_file_base_path'
        );
        if ($result === null) {
            $result = \Hyperframework\APPLICATION_ROOT_PATH
                . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR
                . 'fragment_cache' . DIRECTORY_SEPARATOR;
        }
        return $result;
    }

    public static function getCacheFileBasePath() {
        $result = Config::get(
            'hyperframework.fragment_cache.cache_file_base_path'
        );
        if ($result === null) {
            $result = \Hyperframework\APPLICATION_ROOT_PATH
                . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'cache'
                . DIRECTORY_SEPARATOR . 'fragments' . DIRECTORY_SEPARATOR;
        }
        return $result;
    }

    private static function getBasePath() {
        if (self::$basePath !== null) {
            return self::$basePath;
        }
        if (Config::get('hyperframework.fragment_cache.enable') === false) {
            self::$basePath = self::getSourceFileBasePath();
        } else {
            self::$basePath = self::getCacheFileBasePath();
        }
        return self::$basePath;
    }
}
