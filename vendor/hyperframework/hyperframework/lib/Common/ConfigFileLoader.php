<?php
namespace Hyperframework\Common;

class ConfigFileLoader extends FileLoader {
    protected static function getDefaultBasePath() {
        return parent::getDefaultBasePath() . DIRECTORY_SEPARATOR . 'config';
    }
}
