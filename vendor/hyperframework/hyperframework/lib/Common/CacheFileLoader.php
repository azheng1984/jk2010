<?php
namespace Hyperframework\Common;

use Hyperframework;

class CacheFileLoader extends FileLoader {
    protected static function getDefaultBasePath() {
        return parent::getDefaultBasePath() . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'cache';
    }
}
