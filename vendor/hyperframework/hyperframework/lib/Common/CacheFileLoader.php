<?php
namespace Hyperframework\Common;

class CacheFileLoader extends FileLoader {
    public static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
