<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function loadByEnv(
        $path, $pathConfigName, $isRelativePath = true,
    ) {
        $path = static::appendEnvPath($path);
        return parent::load($path, $pathConfigName, $isRelativePath);
    }

    protected static function appendEnvPath($path) {
        $applicationEnv = Config::get(
            __NAMESPACE__ . '\ApplicationEnv',
            array('default' => array('applicaiton_const' => 'ENV'))
        )
        if ($applicationEnv === null) {
            return $path;
        }
        return 'env' . DIRECTORY_SEPARATOR . $applicationEnv
            . DIRECTORY_SEPARATOR . $path;
    }

    protected static function getDefaultRootPath() {
        return parent::getDefaultRootPath() . DIRECTORY_SEPARATOR . 'config';
    }
}
