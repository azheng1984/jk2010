<?php
namespace Hyperframework\Tool;
use Hyperframework\Cli;

class Bootstrap {
    public static function run($rootPath) {
        define(
            'Hyperframework\Tool\HYPERFRAMEWORK_PATH',
            dirname($rootPath) . DIRECTORY_SEPARATOR
        );
        require HYPERFRAMEWORK_PATH . 'lib' . DIRECTORY_SEPARATOR .
            'Hyperframework' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        Cli\ExceptionHandler::run();
        Cli\Application::run();
    }
}
