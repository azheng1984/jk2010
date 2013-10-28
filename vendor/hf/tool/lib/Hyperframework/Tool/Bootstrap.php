<?php
namespace Hyperframework\Tool;
use Hyperframework\Cli\ExceptionHandler;

class Bootstrap {
    public static function run($rootPath) {
        define(
            'Hyperframework\Tool\HYPERFRAMEWORK_PATH',
            dirname($rootPath) . DIRECTORY_SEPARATOR
        );
        require HYPERFRAMEWORK_PATH . 'lib' . DIRECTORY_SEPARATOR .
            'Hyperframrwork' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
        ClassLoader::run();
        \Hyperframework\Cli\ExceptionHander::run();
    }
}
