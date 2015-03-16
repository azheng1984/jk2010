<?php
namespace Hyperframework\Common;

class FileFullPathBuilder {
    public static function build($path) {
        $path = (string)$path;
        if ($path === ''
            || FileFullPathRecognizer::isFullPath($path) === false
        ) {
            FilePathCombiner::prepend($path, static::getRootPath());
        }
        return $path;
    }

    protected static function getRootPath() {
        return Config::getAppRootPath();
    }
}
