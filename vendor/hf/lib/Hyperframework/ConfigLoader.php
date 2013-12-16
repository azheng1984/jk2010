<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function load(
        $pathConfigName, $defaultPath, $hasEnv = false
    ) {
        if ($hasEnv) {
            $defaultPath = static::appendEnvPath($defaultPath);
        }
        return parent::load(
            $pathConfigName, 'config', $defaultPath, '.config.php'
        );
    }

    private static function appendEnvPath($defaultPath) {
        $appEnv = Config::get(__NAMESPACE__ . '\AppEnv');
        if ($appEnv !== null) {
            $defaultPath = $appEnv . DIRECTORY_SEPARATOR . $defaultPath;
        }
        return 'env' . DIRECTORY_SEPARATOR . $defaultPath;
    }
}
