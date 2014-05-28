<?php
namespace Hyperframework;

class PhpCacheFileLoader extends PhpDataFileLoader {
    protected static function getDefaultBasePath() {
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
