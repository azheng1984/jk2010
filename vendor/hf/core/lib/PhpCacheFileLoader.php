<?php
namespace Hyperframework;

class PhpCacheFileLoader extends PhpDataFileLoader {
    protected static function getDefaultRootPathSuffix() {
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
