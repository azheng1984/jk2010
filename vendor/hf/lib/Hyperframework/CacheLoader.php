<?php
namespace Hyperframework;

class CacheLoader extends DataLoader {
    public static function load($pathConfigName, $defaultPath) {
        return parent::load($pathConfigName, $defaultPath, 'cache');
    }
}
