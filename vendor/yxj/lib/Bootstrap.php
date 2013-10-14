<?php
namespace Yxj;

class Bootstrap {
    public static function run($rootPath) {
        require $rootPath . 'config' . DIRECTORY_SEPARATOR . 'env.config.php';
        Hyperframework\Config::setRootPath($rootPath);
        require HYPERFRAMEWORK_PATH . 'ClassLoader.php';
        Hyperframework\ClassLoader::run();
        Hyperframework\ExceptionHandler::run();
    }
}
