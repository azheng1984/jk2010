<?php
namespace Hyperframework\Common;

class FileFullPathBuilder {
    /**
     * @param string $path
     * @return string
     */
    public static function build($path) {
        if (FileFullPathRecognizer::isFullPath($path) === false) {
            FilePathCombiner::prepend($path, static::getRootPath());
        }
        return $path;
    }

    /**
     * @return string
     */
    protected static function getRootPath() {
        return Config::getAppRootPath();
    }
}
