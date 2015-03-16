<?php
namespace Hyperframework\Common;

class FileLoader {
    public static function loadPhp($path) {
        $path = static::getFullPath($path);
        return include $path;
    }

    public static function loadData($path) {
        $path = static::getFullPath($path);
        return file_get_contents($path);
    }

    protected static function getFullPath($path) {
        return FileFullPathBuilder::build($path);
    }
}
