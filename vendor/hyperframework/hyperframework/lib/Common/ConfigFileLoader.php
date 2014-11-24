<?php
namespace Hyperframework\Common;

class ConfigFileLoader extends FileLoader {
    protected static function getDefaultBasePath() {
        return Config::get('hyperframework.app_root_path')
            . DIRECTORY_SEPARATOR . 'config';
    }
}
