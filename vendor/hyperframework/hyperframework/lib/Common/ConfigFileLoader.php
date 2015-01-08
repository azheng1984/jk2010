<?php
namespace Hyperframework\Common;

class ConfigFileLoader extends FileLoader {
    protected static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR . 'config';
    }
}
