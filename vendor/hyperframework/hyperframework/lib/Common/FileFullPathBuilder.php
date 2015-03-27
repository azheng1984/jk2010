<?php
namespace Hyperframework\Common;

class FileFullPathBuilder {
    public static function build($path) {
        if (FileFullPathRecognizer::isFullPath($path) === false) {
            FilePathCombiner::prepend($path, static::getRootPath());
        }
        return $path;
    }

    protected static function getRootPath() {
        return Config::getAppRootPath();
    }
}
