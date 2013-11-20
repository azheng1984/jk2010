<?php
namespace Hyperframework;

class CacheLoader {
    public static function load($pathConfigName, $defaultPath) {
        return static::load('cache', $pathConfigName, $defaultPath);
    }

    protected static function getDefaultRootPath() {
        return Config::get(
            'Hyperframework\AppPath', array('is_nullable' => false)
        ) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache';
    }
}
