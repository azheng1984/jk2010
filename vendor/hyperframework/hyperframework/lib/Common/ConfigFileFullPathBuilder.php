<?php
namespace Hyperframework\Common;

class ConfigFileFullPathBuilder  extends FileFullPathBuilder {
    protected static function getRootPath() {
        return Config::getAppRootPath() . DIRECTORY_SEPARATOR . 'config';
    }
}
