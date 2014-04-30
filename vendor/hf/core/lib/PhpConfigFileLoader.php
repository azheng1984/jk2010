<?php
namespace Hyperframework;

class PhpConfigFileLoader extends PhpDataFileLoader {
    protected static function getDefaultRootPathSuffix() {
        return DIRECTORY_SEPARATOR . 'config';
    }
}
