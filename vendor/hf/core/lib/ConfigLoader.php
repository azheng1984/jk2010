<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    protected static function getDefaultRootPath() {
        return Config::getApplicationPath() . DIRECTORY_SEPARATOR . 'config';
    }
}
