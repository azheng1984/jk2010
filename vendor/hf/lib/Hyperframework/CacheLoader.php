<?php
namespace Hyperframework;

class CacheLoader extends DataLoader {
    public static function load(
        $path,
        $pathConfigName,
        $isRelativePath = true,
        $extension = '.cache.php'
    ) {
        return parent::load(
            $path, $pathConfigName, $isRelativePath, $extension
        );
    }

    protected static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR
            . 'data' . DIRECTORY_SEPARATOR . 'cache';
    }
}
