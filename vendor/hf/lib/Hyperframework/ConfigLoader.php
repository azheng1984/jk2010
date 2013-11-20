<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function load(
        $pathConfigName, $defaultPath, $hasEnv = false
    ) {
        if ($hasEnv) {
            $defaultPath = Config::get(
                __NAMESPACE__ . '\AppEnv', array('is_nullable' => false)
            ) . DIRECTORY_SEPARATOR . $defaultPath;
        }
        return static::load('config', $pathConfigName, $defaultPath);
    }

    protected static function getDefaultRootPath() {
        return Config::get(
            'Hyperframework\AppPath', array('is_nullable' => false)
        ) . DIRECTORY_SEPARATOR . 'config';
    }
}
