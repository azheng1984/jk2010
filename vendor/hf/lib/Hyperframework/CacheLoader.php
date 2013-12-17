<?php
namespace Hyperframework;

class CacheLoader extends DataLoader {
    protected static function getDefaultRootPath() {
        return 'data' . DIRECTORY_SEPARATOR . 'cache';
    }

    protected static function getDefaultFileNameExtension() {
        return '.cache.php';
    }
}
