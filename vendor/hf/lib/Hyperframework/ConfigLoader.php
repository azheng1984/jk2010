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

    protected static function getDefaultRelativePathByApp() {
        return 'config';
    }

    protected static function getDefaultFileNameExtension() {
        return '.config.php';
    }

    protected static function getEnvPath() {
        return Config::get(
            __NAMESPACE__ . '\AppEnv',
            array('default' => array('app_const' => 'ENV'))
        );
    }

    private static function appendEnvPath($defaultPath) {
        $appEnv = static::getEnvPath();
        if ($appEnv !== null) {
            $defaultPath = $appEnv . DIRECTORY_SEPARATOR . $defaultPath;
        }
        return 'env' . DIRECTORY_SEPARATOR . $defaultPath;
    }
}
