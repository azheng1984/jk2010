<?php
namespace Hyperframework\Common;

use Hyperframework;

class CacheFileLoader extends FileLoader {
    protected static function getDefaultBasePath() {
        return Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
