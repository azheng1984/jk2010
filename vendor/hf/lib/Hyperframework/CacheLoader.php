<?php
namespace Hyperframework;

class CacheLoader extends DataLoader {
    protected static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR
            . 'data' . DIRECTORY_SEPARATOR . 'cache';
    }
}
