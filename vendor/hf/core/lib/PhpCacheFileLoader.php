<?php
namespace Hyperframework;

class PhpCacheFileLoader extends PhpDataFileLoader {
    protected static function getDefaultRootPathSuffix() {
        return DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'cache';
    }
}
