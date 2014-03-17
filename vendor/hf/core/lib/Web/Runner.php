<?php
namespace Hyperframework\Web;

class Runner {
    public static function run($applicationNamespace, $config = array()) {
        static::initialize($applicationNamespace, $config);
        $path = static::getPath();
        if (static::isAsset($path)) {
            static::runAssetProxy($path);
            return;
        }
        static::runApplication($path);
    }

    protected static function initialize($applicationNamespace, $config) {
        $hyperframeworkPath = dirname(__DIR__);
        require $hyperframeworkPath . DIRECTORY_SEPARATOR . 'Config.php';
        $config['Hyperframework\ApplicationNamespace'] = $applicationNamespace;
        \Hyperframework\Config::set($config);
        require $hyperframeworkPath . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        ExceptionHandler::run();
    }

    protected static function getPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if (strncmp($result, '#', 1) === 0) {
            throw new NotFoundException;
        }
        return $result;
    }

    protected static function isAsset($path) {
        return strncmp($path, '/asset/', 7) === 0;
    }

    protected static function runAssetProxy() {
        Asset\AssetProxy::run($path);
    }

    protected static function runApplication($path) {
        Application::run($path);
    }
}
