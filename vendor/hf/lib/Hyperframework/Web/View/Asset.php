<?php
namespace Hyperframework\Web\View;

class Asset {
    public static function renderJsLink($path) {
        //check path namespace
        $pathPrefix = \Hyperframework\Config::get(
            __CLASS__ . '\PathPrefix', ['default' => '/asset/js/']
        );
        if (\Hyperframework\Config::get(__CLASS__ . '\CacheVersionEnabled')) {
            echo '<script src="' . $pathPrefix . $path . '-' .
                static::getCacheVersion($path) . '.js" ></script>';
        }
        echo '<script src="' . $pathPrefix . $path . '.js" ></script>';
    }

    public static function getCacheVersion($path) {
        return 1;
    }
}
