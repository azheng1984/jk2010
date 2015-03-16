<?php
namespace Hyperframework\Common;

class ConfigFileLoader extends FileLoader {
    protected static function getFullPath($path) {
        return ConfigFileFullPathBuilder::build($path);
    }
}
