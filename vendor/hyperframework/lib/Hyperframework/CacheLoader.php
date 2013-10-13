<?php
namespace Hyperframework;

class CacheLoader {
    public static function load($pathName, $defaultPath) {
        $cacheProvider = Config::get(__CLASS__ . '\CacheProvider');
        if ($cacheProvider !== null) {
            $path = Config::get($pathName, array('default' => $defaultPath));
            return $cacheProvider::get($path);
        }
        $path = Config::get($pathName);
        if ($path === null) {
            $path = Config::getCachePath() . $defaultPath. '.cache.php';
        }
        return require $path;
    }
}
