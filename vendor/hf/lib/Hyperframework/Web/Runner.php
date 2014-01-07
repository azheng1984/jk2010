<?php
namespace Hyperframework\Web;

abstract class Runner {
    public static function runApp() {
        static::initialize();
        static::rewriteMethod();
        Application::run(static::getRequestPath());
    }

    public static function runAssetProxy() {
        static::initialize();
        AssetProxy::run();
    }

    protected static function initialize($appPath, $hyperframeworkPath) {
        \Hyperframework\Config::set(
            'Hyperframework\AppPath', static::getAppPath()
        );
        require static::getHyperframeworkPath()
            . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        ExceptionHandler::run();
    }

    protected static function rewriteMethod() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
            $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
        }
    }

    protected static function getRequestPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if (strncmp($result, '#', 1) === 0) {
            throw new Exceptions\NotFoundException;
        }
        return $result;
    }

    protected static function getHyperframeworkPath() {
        return LIB_PATH;
    }

    protected static function getAppPath() {
        return APP_PATH;
    }
}
