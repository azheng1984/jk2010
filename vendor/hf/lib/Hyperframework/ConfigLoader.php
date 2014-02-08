<?php
namespace Hyperframework;

class ConfigLoader extends DataLoader {
    public static function loadByEnv($defaultPath, $pathConfigName) {
        $applicationEnv = Config::get(
            __NAMESPACE__ . '\ApplicationEnv',
            array('default' => array('applicaiton_const' => 'ENV'))
        )
        if ($applicationEnv !== null) {
            $defaultPath = 'env' . DIRECTORY_SEPARATOR . $applicationEnv
                . DIRECTORY_SEPARATOR . $defaultPath;
        }
        return parent::load($defaultPath, $pathConfigName);
    }

    protected static function getDefaultRootPath() {
        return Config::getApplicationPath() . DIRECTORY_SEPARATOR . 'config';
    }
}
