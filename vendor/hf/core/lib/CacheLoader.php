<?php
namespace Hyperframework;

class CacheLoader extends DataLoader {
    protected static function getDefaultRootPathSuffix() {
        return DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache';
    }
}
