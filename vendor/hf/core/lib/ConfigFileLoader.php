<?php
namespace Hyperframework;

class ConfigFileLoader extends FileLoader {
    protected static function getDefaultBasePath() {
        return APPLICATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'config';
    }
}
