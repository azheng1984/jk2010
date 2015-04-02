<?php
namespace Hyperframework\Common;

class CacheFileLoader extends FileLoader {
    /**
     * @param string $path
     * @return string
     */
    protected static function getFullPath($path) {
        return CacheFileFullPathBuilder::build($path);
    }
}
