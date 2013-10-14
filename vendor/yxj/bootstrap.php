<?php
namespace Yxj\Bootstrap;

public function run() {
   $rootPath = __DIR__ . DIRECTORY_SEPARATOR;
   require $rootPath . 'config' . DIRECTORY_SEPARATOR . 'env.config.php';
   Hyperframework\Config::setRootPath($rootPath);
   require HYPERFRAMEWORK_PATH . 'ClassLoader.php';
   Hyperframework\ClassLoader::run();
   Hyperframework\ExceptionHandler::run();
}

run();
