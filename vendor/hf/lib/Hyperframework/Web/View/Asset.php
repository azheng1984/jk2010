<?php
namespace Hyperframework\Web\View;

//Asset::getPath('common');
//Asset::getPath('/list');
//Asset::getPath('jquery.1.1.2.min');
//Asset::getPath('http://localhost/jquery.1.1.2.min');

class Asset {
    public static function getPath($fileName) {
        $rootPath = '';
        if (substr($path, 0, 1) !== '/' && substr($path, 0, 7) !== 'http://') {
            $rootPath = \Hyperframework\Config::get(
                __CLASS__ . '\JsRootPath', array('default' => '/asset/js/')
            );
        }
        //check path namespace
        if (\Hyperframework\Config::get(__CLASS__ . '\CacheVersionEnabled')) {
            echo '<script src="' . $rootPath . $name. '-'
                . AssetCache::getVersion($path)
                . '.' . $extension . '"></script>';
            return;
        }
        echo '<script src="' . $rootPath . $path . '.' . $extension . '"></script>';
    }
}
