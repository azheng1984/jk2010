<?php
namespace Hyperframework;

class ConfigFileLoader extends FileLoader {
    protected static function getDefaultBasePath() {
        return APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'config';
    }
}
