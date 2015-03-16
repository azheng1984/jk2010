<?php
namespace Hyperframework\Common;

class CacheFileLoader extends FileLoader {
    protected static function getFullPath($path) {
        return CacheFileFullPathBuilder::build($path);
    }
}
