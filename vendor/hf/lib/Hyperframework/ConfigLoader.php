<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function load($pathConfigName, $defaultPath) {
        parent::load($pathConfigName, $defaultPath, 'config');
    }
}
