<?php
namespace Hyperframework;

class PhpConfigFileLoader extends PhpDataFileLoader {
    protected static function getDefaultRootPath() {
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . 'config';
    }
}
