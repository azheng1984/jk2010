<?php
namespace Hyperframework\Web;

class Runner {
    public static function run($hyperframeworkPath, $appPath) {
        static::initialize($hyperframeworkPath, $appPath);
        static::rewriteMethod();
        $path = static::getPath();
        if (static::isAsset($path)) {
            Asset\AssetProxy::run($path);
            return;
        }
        Application::run($path);
    }

    protected static function isAsset($path) {
        return strncmp($path, '/asset/', 7) === 0;
    }

    protected static function initialize($hyperframeworkPath, $appPath) {
        \Hyperframework\Config::set('Hyperframework\AppPath', $appPath);
        require $hyperframeworkPath . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        ExceptionHandler::run();
    }

    protected static function rewriteMethod() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
            $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
        }
    }

    protected static function getPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if (strncmp($result, '#', 1) === 0) {
            throw new Exceptions\NotFoundException;
        }
        return $result;
    }
}
