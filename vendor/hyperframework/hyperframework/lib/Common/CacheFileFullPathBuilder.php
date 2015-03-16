<?php
namespace Hyperframework\Common;

class CacheFileFullPathBuilder extends FileFullPathBuilder {
    protected static function getRootPath() {
        return Config::getAppRootPath() . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
