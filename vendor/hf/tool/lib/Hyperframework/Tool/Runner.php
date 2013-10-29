<?php
namespace Hyperframework\Tool;

class Runner {
    public static function run($rootPath) {
        $hyperframeworkPath = dirname($rootPath) . DIRECTORY_SEPARATOR . 'lib' .
            DIRECTORY_SEPARATOR . 'Hyperframework' . DIRECTORY_SEPARATOR;
        require $hyperframeworkPath . 'Config.php';
        \Hyperframework\Config::set('Hyperframework\AppPath', $rootPath);
        require $hyperframeworkPath . 'ClassLoader.php';
        \Hyperframework\ClassLoader::run();
        \Hyperframework\Cli\ExceptionHandler::run();
        \Hyperframework\Cli\Application::run();
    }
}
