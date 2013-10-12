<?php
namespace Hyperframework;

class ConfigLoader {
    public static function load($pathName, $defaultPath) {
        $providerClass = Config::get('Hyperframework\ConfigProvider');
        if ($providerClass !== null) {
            $path = Config::get($pathName, array('default' => $defaultPath);
            return $providerClass::get($path);
        }
        $path = require Config::get($pathName);
        if ($path === null) {
            $path = Config::getConfigPath() . $defaultPath. '.config.php';
        }
        return require $path;
    }
}
