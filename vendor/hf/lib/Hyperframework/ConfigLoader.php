<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function load(
        $path, $pathConfigName, $hasEnv = false, $isRelativePath = true
    ) {
        if ($hasEnv) {
            $path = static::appendEnvPath($path);
        }
        return parent::executeTemplate($path, $pathConfigName, $isRelativePath);
    }

    protected static function getDefaultRootPathSuffix() {
        return 'data' . DIRECTORY_SEPARATOR . 'config';
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
