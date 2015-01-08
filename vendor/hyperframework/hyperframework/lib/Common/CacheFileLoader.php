<?php
namespace Hyperframework\Common;

class CacheFileLoader extends FileLoader {
    protected static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
