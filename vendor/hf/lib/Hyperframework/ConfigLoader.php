<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function load(
        $path,
        $pathConfigName,
        $hasEnv = false,
        $isRelativePath = true,
        $extension = '.config.php'
    ) {
        if ($hasEnv) {
            $path = static::appendEnvPath($path);
        }
        return parent::load(
            $path, $pathConfigName, $isRelativePath, $extension
        );
    }

    protected static function appendEnvPath($path) {
        $appEnv = Config::get(
            __NAMESPACE__ . '\AppEnv',
            array('default' => array('app_const' => 'ENV'))
        )
        if ($appEnv === null) {
            return $path;
        }
        return 'env' . DIRECTORY_SEPARATOR . $appEnv
            . DIRECTORY_SEPARATOR . $path;
    }

    protected static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR . 'config';
    }
}
