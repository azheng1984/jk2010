<?php
namespace Hyperframework;

class ConfigLoader {
    public static function load($pathName, $defaultPath) {
        $configProvider = Config::get(__CLASS__ . '\ConfigProvider');
        if ($configProvider !== null) {
            $path = Config::get($pathName, array('default' => $defaultPath));
            return $configProvider::get($path);
        }
        $path = Config::get($pathName);
        if ($path === null) {
            $path = Config::getConfigPath() . $defaultPath. '.config.php';
        }
        return require $path;
    }
}
