<?php
namespace Hyperframework;

class CacheLoader {
    public static function load($pathConfigName, $defaultPath) {
        return DataLoader::load(
            $pathConfigName,
            'data' . DIRECTORY_SEPARATOR . 'cache'
            $defaultPath,
            'cache',
        );
    }
}
