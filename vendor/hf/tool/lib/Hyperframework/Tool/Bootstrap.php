<?php
namespace Hyperframework\Tool;

class Bootstrap {
    public static function run($rootPath) {
        require dirname($rootPath) . DIRECTORY_SEPARATOR . 'lib' .
            DIRECTORY_SEPARATOR . 'Hyperframework' .
            DIRECTORY_SEPARATOR . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        \Hyperframework\Cli\ExceptionHandler::run();
        \Hyperframework\Cli\Application::run();
    }
}
