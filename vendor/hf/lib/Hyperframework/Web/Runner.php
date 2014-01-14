<?php
namespace Hyperframework\Web;

class Runner {
    public final static function run($hyperframeworkPath, $appNamespace) {
        static::initialize($hyperframeworkPath, $appNamespace);
        $path = static::getPath();
        if (static::isAsset($path)) {
            static::runAssetProxy($path);
            return;
        }
        static::runApp($path);
    }

    protected static function initialize($hyperframeworkPath, $appNamespace) {
        require $hyperframeworkPath . DIRECTORY_SEPARATOR
            . 'Hyperframework' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        \Hyperframework\Config::set(
            'Hyperframework\AppNamespace', $appNamespace
        );
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

    protected static function runApp($path) {
        Application::run($path);
    }
}
