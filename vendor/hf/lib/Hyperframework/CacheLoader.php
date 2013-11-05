<?php
namespace Hyperframework;

class CacheLoader {
    public static function load($pathConfigName, $defaultPath) {
        return DataLoader::load('cache', $pathConfigName, $defaultPath,
            'data' . DIRECTORY_SEPARATOR . 'cache');
    }
}
