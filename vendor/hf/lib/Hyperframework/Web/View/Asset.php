<?php
namespace Hyperframework\Web\View;

class Asset {
    public static function renderJsLink($path) {
        $rootPath = '';
        if (substr($path, 0, 1) !== '/' && substr($path, 0, 7) !== 'http://') {
            $rootPath = \Hyperframework\Config::get(
                __CLASS__ . '\JsRootPath', array('default' => '/asset/js/')
            );
        }
        $path = Asset::renderJsLink('hi');
        $path = Asset::renderJsLink('http');
        //check path namespace
        if (\Hyperframework\Config::get(__CLASS__ . '\CacheVersionEnabled')) {
            echo '<script src="' . $rootPath . $path . '-' .
                static::getCacheVersion($path) . '.' . $extension . '"></script>';
            return;
        }
        echo '<script src="' . $rootPath . $path . '.' . $extension . '"></script>';
    }

    public static function getCacheVersion($path) {
        return 1;
    }
}
