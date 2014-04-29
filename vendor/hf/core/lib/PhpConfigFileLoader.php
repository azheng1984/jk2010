<?php
namespace Hyperframework;

class PhpConfigFileLoader extends PhpDataFileLoader {
    protected static function getDefaultPathPrefix() {
        return DIRECTORY_SEPARATOR . 'config';
    }
}
