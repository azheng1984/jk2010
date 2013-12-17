<?php
namespace Hyperframework;

class CacheLoader extends DataLoader {
    protected static function getDefaultFileNameExtension() {
        return '.cache.php';
    }

    protected static function getDefaultRootPath() {
        return 'data' . DIRECTORY_SEPARATOR . 'cache';
    }
}
