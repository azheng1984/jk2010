<?php
namespace Hyperframework\Common;

class ConfigFileLoader extends FileLoader {
    public static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR . 'config';
    }
}
