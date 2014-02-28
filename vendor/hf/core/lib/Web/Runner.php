<?php
namespace Hyperframework\Web;

class Runner {
    public static function run($applicationNamespace) {
        static::initialize($applicationNamespace);
        $path = static::getPath();
        if (static::isAsset($path)) {
            static::runAssetProxy($path);
            return;
        }
        static::runApplication($path);
    }

    protected static function initialize($applicationNamespace) {
        \Hyperframework\Config::set(
            'Hyperframework\ApplicationNamespace', $applicationNamespace
        );
        require Config::getHyperframeworkPath()
            . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        ExceptionHandler::run();
    }

    protected static function getPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if (strncmp($result, '#', 1) === 0) {
            throw new Exceptions\NotFoundException;
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
