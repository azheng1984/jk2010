<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function load(
        $pathConfigName, $defaultPath, $hasEnv = false
    ) {
        if ($hasEnv) {
            return parent::load(
                $pathConfigName, static::appendEnvPath($defaultPath)
            );
        }
        return parent::load($pathConfigName, $defaultPath);
    }

    protected static function getDefaultFileNameExtension() {
        return '.config.php';
    }

    protected static function getDefaultRootPath() {
        return 'config';
    }

    private static function appendEnvPath($defaultPath) {
        $appEnv = Config::get(__NAMESPACE__ . '\AppEnv');
        if ($appEnv !== null) {
            $defaultPath = $appEnv . DIRECTORY_SEPARATOR . $defaultPath;
        }
        return 'env' . DIRECTORY_SEPARATOR . $defaultPath;
    }
}
