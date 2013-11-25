<?php
namespace Hyperframework;

class ConfigLoader {
    public static function load(
        $pathConfigName, $defaultPath, $hasEnv = false
    ) {
        if ($hasEnv) {
            $defaultPath = Config::get(
                'Hyperframework\AppEnv', array('is_nullable' => false)
            ) . DIRECTORY_SEPARATOR . $defaultPath;
        }
        return DataLoader::load(
            $pathConfigName, 'config', $defaultPath, 'config'
        );
    }
}
