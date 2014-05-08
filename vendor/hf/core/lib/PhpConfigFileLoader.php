<?php
namespace Hyperframework;

class PhpConfigFileLoader extends PhpDataFileLoader {
    protected static function getDefaultRootPathSuffix() {
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . 'config';
    }
}
