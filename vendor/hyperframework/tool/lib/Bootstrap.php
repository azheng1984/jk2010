<?php
namespace Hyperframework\Tool;

class Bootstrap {
    public static function run($rootPath) {
        define(
            'Hyperframework\Tool\LIB_PATH',
            dirname($rootPath) . DIRECTORY_SEPARATOR . 'lib' .
                DIRECTORY_SEPARATOR . 'Hyperframrwork' . DIRECTORY_SEPARATOR
        );
        \Hyperframework\Config::setRootPath($rootPath);
        require LIB_PATH . 'ClassLoader2.php';
        $classLoader = new \Hyperframework\ClassLoader2;
        $classLoader->run();
        $exceptionHandler = new CommandExceptionHandler;
        $exceptionHandler->run();
        if (!isset($_SERVER['PWD'])) {
            $_SERVER['PWD'] = getcwd();
        }
    }
}
