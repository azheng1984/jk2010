<?php
namespace Hyperframework\Common;

use Hyperframework;

class CacheFileLoader extends FileLoader {
    public static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
