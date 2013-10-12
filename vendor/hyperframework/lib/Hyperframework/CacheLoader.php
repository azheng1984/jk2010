<?php
namespace Hyperframework;

class CacheLoader {
    public static function load($pathName, $defaultPath) {
        $providerClass = Config::get('Hyperframework\CacheProvider');
        if ($providerClass !== null) {
            $path = Config::get($pathName, array('default' => $defaultPath));
            return $providerClass::get($path);
        }
        $path = require Config::get($pathName);
        if ($path === null) {
            $path = Config::getCachePath() . $defaultPath. '.cache.php';
        }
        return require $path;
    }
}
