<?php
namespace Hyperframework;

class PhpConfigFileLoader extends PhpDataFileLoader {
    protected static function getDefaultBasePath() {
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . 'config';
    }
}
