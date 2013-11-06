<?php
namespace Hyperframework;

class ConfigLoader {
    public static function load(
        $pathConfigName, $defaultPath, $hasEnv = false
    ) {
        if ($hasEnv) {
            $defaultPath = Config::get(
                __NAMESPACE__ . '\AppEnv', array('is_nullable' => false)
            ) . DIRECTORY_SEPARATOR . $defaultPath;
        }
        return DataLoader::load(
            'config', $pathConfigName, $defaultPath, 'config'
        );
    }
}
