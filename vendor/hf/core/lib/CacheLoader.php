<?php
namespace Hyperframework;

class CacheLoader extends DataLoader {
    protected static function getDefaultRootPath() {
        return Config::getApplicationPath() . DIRECTORY_SEPARATOR
            . 'data' . DIRECTORY_SEPARATOR . 'cache';
    }
}
