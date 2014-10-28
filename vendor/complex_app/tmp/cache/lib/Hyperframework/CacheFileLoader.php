<?php
namespace Hyperframework;

class CacheFileLoader extends FileLoader {
    protected static function getDefaultBasePath() {
        return APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
