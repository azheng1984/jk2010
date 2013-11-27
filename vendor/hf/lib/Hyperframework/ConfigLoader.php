<?php
namespace Hyperframework;

class ConfigLoader {
    public static function load(
        $pathConfigName, $defaultPath, $hasEnv = false
    ) {
        if ($hasEnv) {
            $defaultPath = static::appendEnvPath($defaultPath);
        }
        return DataLoader::load(
            $pathConfigName, 'config', $defaultPath, 'config'
        );
    }

    private static function appendEnvPath($defaultPath) {
        $appEnv = Config::get(__NAMESPACE__ . '\AppEnv');
        if ($appEnv === null) {
            return $defaultPath;
        }
        return $appEnv . DIRECTORY_SEPARATOR . $defaultPath;
    }
}
