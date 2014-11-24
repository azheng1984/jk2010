<?php
namespace Hyperframework\Common;

use Hyperframework;

class ConfigFileLoader extends FileLoader {
    protected static function getDefaultBasePath() {
        return Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'config';
    }
}
