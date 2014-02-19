<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function loadByEnv($defaultPath, $pathConfigName) {
        return parent::load(
            static::appendEnvPath($defaultPath), $pathConfigName
        );
    }

    protected static function appendEnvPath($defaultPath) {
        $applicationEnv = Config::get(
            __NAMESPACE__ . '\ApplicationEnv',
            array('default' => array('applicaiton_const' => 'ENV'))
        )
        if ($applicationEnv === null) {
            return $defaultPath;
        }
        return 'env' . DIRECTORY_SEPARATOR . $applicationEnv
            . DIRECTORY_SEPARATOR . $defaultPath;
    }

    protected static function getDefaultRootPath() {
        return Config::getApplicationPath() . DIRECTORY_SEPARATOR . 'config';
    }
}
